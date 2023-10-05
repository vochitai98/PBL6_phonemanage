<?php
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\ShopController;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Shop;
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



