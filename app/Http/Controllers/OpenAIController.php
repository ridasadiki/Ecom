<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use GuzzleHttp\Client;

class OpenAIController extends Controller
{
    public function showForm()
    {
        $categories = Category::all();
        return view('products.form', compact('categories'));
    }

    public function generateProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'keywords' => 'required|string'
            ]);

            $category = Category::find($validated['category_id']);
            if (!$category) {
                return Response::json([
                    'error' => 'Category not found'
                ], 404);
            }

            $prompt = "Generate a product for the {$category->name} category with these features: {$validated['keywords']}. " .
                     "Format the response as a valid JSON object with exactly these fields:\n" .
                     "{\n" .
                     "  \"name\": \"product name here\",\n" .
                     "  \"description\": \"detailed product description here\",\n" .
                     "  \"price\": number\n" .
                     "}\n" .
                     "Make the description detailed and marketing-focused. Price should be realistic for the product type. " .
                     "Return ONLY the JSON object, no other text.";

            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=' . env('GOOGLE_API_KEY'), [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.8,
                    'maxOutputTokens' => 1000,
                ]
            ]);

            $data = $response->json();

            if (isset($data['error'])) {
                return Response::json([
                    'error' => 'Google API Error: ' . $data['error']['message']
                ], 400);
            }

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return Response::json([
                    'error' => 'AI response is missing expected data',
                    'raw_response' => json_encode($data)
                ], 400);
            }

            $assistantReply = $data['candidates'][0]['content']['parts'][0]['text'];
            $assistantReply = preg_replace('/```json\s*|\s*```/', '', trim($assistantReply));
            
            $productData = json_decode($assistantReply, true);

            if (!is_array($productData)) {
                return Response::json([
                    'error' => 'Failed to parse AI response as JSON',
                    'raw_response' => $assistantReply
                ], 400);
            }

            if (!isset($productData['name']) || !isset($productData['description']) || !isset($productData['price'])) {
                return Response::json([
                    'error' => 'AI response missing required fields',
                    'data' => $productData
                ], 400);
            }

            return Response::json([
                'success' => true,
                'product' => $productData
            ]);

        } catch (\Exception $e) {
            return Response::json([
                'error' => 'Failed to generate product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateImage(Request $request)
    {
        try {
            $request->validate([
                'prompt' => 'required|string'
            ]);

            $apiKey = env('HUGGINGFACE_API_KEY');
            
            if (!$apiKey) {
                Log::error("API Key is missing in environment");
                return Response::json([
                    'error' => 'Hugging Face API key is not set in .env file'
                ], 500);
            }

            if (!str_starts_with($apiKey, 'hf_')) {
                return Response::json([
                    'error' => 'Invalid Hugging Face API key format. It must start with "hf_"'
                ], 500);
            }

            Log::info('Generating image with prompt: ' . $request->prompt);

            $client = new Client([
                'verify' => false,
                'timeout' => 90,
                'connect_timeout' => 30
            ]);

            $response = $client->post('https://api-inference.huggingface.co/models/stabilityai/stable-diffusion-3-medium-diffusers', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'inputs' => $request->prompt,
                    'wait_for_model' => true,
                    'options' => [
                        'wait_for_model' => true,
                        'use_cache' => true
                    ]
                ]
            ]);

            Log::info('Response status: ' . $response->getStatusCode());
            Log::info('Response headers: ' . json_encode($response->getHeaders()));

            if ($response->getStatusCode() !== 200) {
                $error = $response->getBody()->getContents();
                Log::error('Image generation failed: ' . $error);

                if (str_contains($error, '<!DOCTYPE')) {
                    Log::error('Received HTML error page, likely an authentication issue');
                    return Response::json([
                        'error' => 'Authentication failed. Please check your Hugging Face API key.'
                    ], 401);
                }

                try {
                    $errorData = json_decode($error, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $errorMessage = $errorData['error'] ?? $error;
                    } else {
                        $errorMessage = $error;
                    }
                } catch (\Exception $e) {
                    $errorMessage = $error;
                }

                return Response::json([
                    'error' => 'Failed to generate image: ' . $errorMessage
                ], $response->getStatusCode());
            }

            $imageData = $response->getBody()->getContents();

            $contentType = $response->getHeaderLine('Content-Type');
            if (!$contentType || !str_contains($contentType, 'image/')) {
                Log::error('Invalid response type: ' . $contentType);
                Log::error('Response body (first 1000 chars): ' . substr($imageData, 0, 1000));
                return Response::json([
                    'error' => 'Unexpected response type from image generation API'
                ], 400);
            }

            if (!Storage::disk('public')->exists('products')) {
                Storage::disk('public')->makeDirectory('products');
            }

            $extension = str_contains($contentType, 'image/jpeg') ? 'jpg' : 'png';
            $filename = 'products/' . uniqid() . '.' . $extension;
            
            if (!Storage::disk('public')->put($filename, $imageData)) {
                Log::error('Failed to save image to storage');
                return Response::json([
                    'error' => 'Failed to save generated image'
                ], 500);
            }

            Log::info('Image saved successfully: ' . $filename);

            if (!Storage::disk('public')->exists($filename)) {
                Log::error('Saved image file not found: ' . $filename);
                return Response::json([
                    'error' => 'Generated image file not found after saving'
                ], 500);
            }

            return $filename;

        } catch (\Exception $e) {
            Log::error('Image generation error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    public function storeProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'image' => 'nullable|string'
            ]);

            $product = Product::create($validated);

            return Response::json([
                'success' => true,
                'message' => 'Product created successfully!',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'error' => 'Failed to save product: ' . $e->getMessage()
            ], 500);
        }
    }
}
