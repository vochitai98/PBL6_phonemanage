<?php
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\Product_OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\PromotionController;
use App\Http\Controllers\API\ShopController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\Shop_ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Brands
Route::middleware(['admin'])->group(function () {
    
    Route::resource('brands', BrandController::class);
    Route::get('search/brands',[BrandController::class,'search']);
});

//Customers
Route::middleware(['admin'])->group(function () {
    
    Route::resource('customers', CustomerController::class);
    Route::get('search/customers',[CustomerController::class,'search']);
});

Route::middleware(['admin'])->group(function () {

    Route::resource('shops', ShopController::class);
    Route::get('search/shops',[ShopController::class,'search']);
});
//Products
Route::middleware(['admin'])->group(function () {
    
    Route::resource('products', ProductController::class);
    //Route::get('search/products',[ProductController::class,'search']);
});
//Reivew
Route::middleware(['admin'])->group(function () {
    
    Route::resource('reviews', ReviewController::class);
    //Route::get('search/reviews',[ProductController::class,'search']);
});
//Promotion
Route::middleware(['admin'])->group(function () {
    
    Route::resource('promotions', PromotionController::class);
    //Route::get('search/promotions',[ProductController::class,'search']);
});
//Shop_Product
Route::middleware(['admin'])->group(function () {
    
    Route::resource('shop_products', Shop_ProductController::class);
    //Route::get('search/promotions',[ProductController::class,'search']);
});
//Order
Route::middleware(['admin'])->group(function () {
    
    Route::resource('orders', OrderController::class);
    //Route::get('search/promotions',[ProductController::class,'search']);
});

//Product_Order
Route::middleware(['admin'])->group(function () {
    
    Route::resource('product_orders', Product_OrderController::class);
    //Route::get('search/promotions',[ProductController::class,'search']);
});


