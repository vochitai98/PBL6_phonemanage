<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CheckoutPayment;
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
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
    Route::post('admins/me', [AdminController::class, 'me']);
    Route::get('admins', [AdminController::class, 'index']);
    Route::get('admins/{id}', [AdminController::class, 'show']);
    Route::post('admins/register', [AdminController::class, 'register']);
    Route::post('admins/login', [AdminController::class, 'login']);
    Route::post('admins/logout', [AdminController::class, 'logout'])->middleware('adminAccess');

    Route::post('customers/register', [CustomerController::class, 'register']);
    Route::post('customers/login', [CustomerController::class, 'login']);
    Route::post('customers/logout', [CustomerController::class, 'logout'])->middleware('customerAccess');
    Route::post('customers/me', [CustomerController::class, 'me']);



//Admin Manage
Route::middleware('adminAccess')->group(function () {
    //Brands
    Route::get('brands', [BrandController::class, 'index'])->withoutMiddleware('adminAccess');
    Route::get('brands/{id}', [BrandController::class, 'show']);
    Route::post('brands', [BrandController::class, 'store']);
    Route::put('brands/{id}', [BrandController::class, 'update']);
    Route::delete('brands/{id}', [BrandController::class, 'destroy']);
    Route::get('search/brands', [BrandController::class, 'search'])->withoutMiddleware('adminAccess');
    //Products
    Route::get('products', [ProductController::class, 'index'])->withoutMiddleware('adminAccess');
    Route::get('products/{id}', [ProductController::class, 'show'])->withoutMiddleware('adminAccess');
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    Route::get('search/products', [ProductController::class, 'search'])->withoutMiddleware('adminAccess');
    //Customer
    Route::get('search/customers', [CustomerController::class, 'search']);
    Route::get('customers', [CustomerController::class, 'index']);
    Route::delete('customers/{id}', [CustomerController::class, 'destroy']);
    //Shop
    
});

    


//Customers
Route::middleware('customerAccess')->group(function () {
    Route::get('customers/{id}', [CustomerController::class, 'show']);
    Route::post('customers', [CustomerController::class, 'store'])->withoutMiddleware('customerAccess');
    Route::put('customers/{id}', [CustomerController::class, 'update']);


    //Product_Order
    Route::get('product_orders', [Product_OrderController::class, 'index']);
    Route::get('product_orders/{id}', [Product_OrderController::class, 'show']);
    Route::post('product_orders', [Product_OrderController::class, 'store']);
    Route::put('product_orders/{id}', [Product_OrderController::class, 'update'])->withoutMiddleware('customerAccess');
    Route::delete('product_orders/{id}', [Product_OrderController::class, 'destroy'])->withoutMiddleware('customerAccess');
    //Route::get('search/product_orders',[Product_OrderController::class,'search']);
    Route::post('/cart/add-product', [Product_OrderController::class, 'addToCart'])->withoutMiddleware('customerAccess');
    Route::get('/view-cart', [Product_OrderController::class, 'viewCart'])->withoutMiddleware('customerAccess');
    Route::get('/in_decreaseAmount', [Product_OrderController::class, 'in_decreaseAmount'])->withoutMiddleware('customerAccess');
});

    

//Shops
Route::middleware('access_shop')->group(function () {
    Route::get('shops/{id}', [ShopController::class, 'show'])->withoutMiddleware('access_shop'); 
    Route::put('shops/{id}', [ShopController::class, 'update']);
    Route::delete('shops/{id}', [ShopController::class, 'destroy']);
    Route::get('search/shops', [ShopController::class, 'search'])->withoutMiddleware('access_shop');
    Route::get('quanlyshop/shops', [ShopController::class, 'getShopByCustomerId']);
    //Route::post('shops/payment/vnpay', [CheckoutPayment::class, 'payment_vnpay']);
    //Route::post('shops/payment/momo', [CheckoutPayment::class, 'payment_momo']);
});
    Route::post('shops', [ShopController::class, 'store']);
    Route::get('shops', [ShopController::class, 'index']);
    

//Reivew

    Route::get('reviews', [ReviewController::class, 'index']);
    Route::get('reviews/{id}', [ReviewController::class, 'show']);
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::put('reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);

//Promotion

    Route::get('promotions', [PromotionController::class, 'index']);
    Route::get('promotions/{id}', [PromotionController::class, 'show']);
    Route::post('promotions', [PromotionController::class, 'store']);
    Route::put('promotions/{id}', [PromotionController::class, 'update']);
    Route::delete('promotions/{id}', [PromotionController::class, 'destroy']);
    Route::get('getPromotionByIdCutomer', [PromotionController::class, 'getPromotionByIdCutomer']);
    

//Shop_Product
//Shops
    Route::get('shop_products', [Shop_ProductController::class, 'index']);
    Route::get('shop_products/{id}', [Shop_ProductController::class, 'show']);
    Route::post('shop_products', [Shop_ProductController::class, 'store']);
    Route::put('shop_products/{id}', [Shop_ProductController::class, 'update'])->middleware('access_shop_product');
    Route::delete('shop_products/{id}', [Shop_ProductController::class, 'destroy'])->middleware('access_shop_product');
    Route::get('search/shop_products', [Shop_ProductController::class, 'search']);
    Route::get('searchByPrice/shop_products', [Shop_ProductController::class, 'searchByPrice']);
    Route::get('shop_productByIdCustomer', [Shop_ProductController::class, 'getShop_productByIdCutomer']);
    
//Order

    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::put('orders/{id}', [OrderController::class, 'update']);
    Route::delete('orders/{id}', [OrderController::class, 'destroy']);
    //Route::get('search/orders',[OrderController::class,'search']);

   

    