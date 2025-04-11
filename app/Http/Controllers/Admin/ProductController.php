<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products,sku'
        ]);

        if (!$request->hasFile('image')) {
            $imagePrompt = new Request([
                'prompt' => $validated['name'] . '. ' . substr($validated['description'], 0, strpos($validated['description'], '.') ?: strlen($validated['description'])) . '. Professional product photography, white background, high resolution.'
            ]);
            $imagePath = $this->generateImage($imagePrompt);
            $validated['image'] = $imagePath;
        } else {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        if (!isset($validated['sku'])) {
            $validated['sku'] = strtoupper(Str::random(8));
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if (!isset($validated['sku'])) {
            $validated['sku'] = strtoupper(Str::random(8));
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function showGenerateForm()
    {
        $categories = Category::all();
        return view('admin.products.generate', compact('categories'));
    }

    public function generateProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'keywords' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'image_style' => 'nullable|string'
            ]);

            $name = ucwords($validated['keywords']);

            $category = Category::find($validated['category_id']);
            $description = $this->generateDescription($name, $category->name);
            
            $price = $this->generatePrice($category->name);

            $imagePrompt = $name . '. ' . ($validated['image_style'] ?? 'Professional product photography, white background, high resolution.');
            
            $client = new Client(['verify' => false]);
            $response = $client->post('https://api-inference.huggingface.co/models/stabilityai/stable-diffusion-3-medium-diffusers', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => $imagePrompt,
                    'parameters' => [
                        'negative_prompt' => 'text, watermark, logo, label, blur, distortion, low quality, bad quality',
                        'num_inference_steps' => 50,
                        'guidance_scale' => 7.5
                    ]
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to generate image');
            }

            $imageData = $response->getBody()->getContents();
            $imageName = 'products/' . Str::random(40) . '.png';
            Storage::disk('public')->put($imageName, $imageData);

            $product = Product::create([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $validated['category_id'],
                'image' => $imageName,
                'sku' => strtoupper(Str::random(8))
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product generated successfully',
                'product' => array_merge(
                    $product->toArray(),
                    ['category_name' => $category->name]
                )
            ]);

        } catch (\Exception $e) {
            Log::error('Product generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate product: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateDescription($name, $categoryName)
    {
        $descriptions = [
            'Chairs' => "Experience the epitome of comfort with our {$name}. A beautifully crafted chair that combines style with ergonomic design, premium materials, and exceptional build quality.",
            'Tables' => "Introducing the {$name}, an elegant table that adds sophistication to any space. Built with high-quality materials and meticulous attention to detail.",
            'Sofas' => "Discover luxury with our {$name}, a premium sofa offering supreme comfort and style. Perfect for modern living spaces with its timeless design.",
            'Beds' => "Elevate your sleep experience with the {$name}, a premium bed that ensures restful sleep with its superior design and quality construction.",
            'Storage' => "Transform your space with the {$name}, a versatile storage solution that combines functionality with elegant design. Perfect for organizing with style.",
            'Lighting' => "Illuminate your space with the {$name}, a stunning lighting fixture that creates the perfect ambiance while complementing your decor."
        ];

        return $descriptions[$categoryName] ?? "Introducing the {$name}, a premium quality product designed with attention to detail and superior craftsmanship.";
    }

    private function generatePrice($categoryName)
    {
        $priceRanges = [
            'Chairs' => ['min' => 199, 'max' => 999],
            'Tables' => ['min' => 499, 'max' => 1999],
            'Sofas' => ['min' => 899, 'max' => 2999],
            'Beds' => ['min' => 799, 'max' => 2499],
            'Storage' => ['min' => 299, 'max' => 1499],
            'Lighting' => ['min' => 99, 'max' => 599]
        ];

        $range = $priceRanges[$categoryName] ?? ['min' => 199, 'max' => 999];
        return rand($range['min'], $range['max']);
    }

    private function generateImage(Request $request)
    {

    }
}
