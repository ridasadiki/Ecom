<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Broadcast Authentication Route
Broadcast::routes(['middleware' => ['web', 'auth']]);

// Product Routes (Public)
Route::get('/', function () {
    return redirect()->route('products.index');
})->name('home');

Route::get('/home', function () {
    return redirect()->route('products.index');
});

// Product Routes
Route::resource('products', ProductController::class)->only(['index', 'show']);

// Cart Routes
Route::middleware(['auth'])->group(function () {
    // Wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/{wishlistItem}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');

    // Reviews routes
    Route::post('products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
});

// Category Routes
Route::resource('categories', CategoryController::class);

// Order Routes
Route::resource('orders', OrderController::class);
Route::put('/orders/{order}/update-delivery-status', [OrderController::class, 'updateDeliveryStatus']);

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Customer routes
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);
    
    // Product routes - custom routes before resource
    Route::get('products/generate', [AdminProductController::class, 'showGenerateForm'])->name('products.generate');
    Route::post('products/generate', [AdminProductController::class, 'generateProduct'])->name('products.generate.store');
    Route::resource('products', AdminProductController::class);
    
    // Category routes - custom routes before resource
    Route::get('categories/create/quick', [AdminCategoryController::class, 'createQuick'])->name('categories.create.quick');
    Route::post('categories/store/quick', [AdminCategoryController::class, 'storeQuick'])->name('categories.store.quick');
    Route::resource('categories', AdminCategoryController::class);

    // Admin chat routes
    Route::get('/chat', [ChatController::class, 'adminIndex'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'adminStore'])->name('chat.store');
    Route::get('/chat/messages/{userId}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::get('/chat/users', [ChatController::class, 'getUsers'])->name('chat.users');
});

// API Routes
Route::post('/generate-image', [OpenAIController::class, 'generateImage'])->name('generate.image');
Route::get('/generate-prompt/{category}/{keys}', [OpenAIController::class, 'getPrompt'])->name('getPrompt');

Auth::routes();
