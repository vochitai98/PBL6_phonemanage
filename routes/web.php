<?php
use App\Http\Controllers\API\CheckoutPayment;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
    
});
Route::get('/payment', function () {
    return view('payment');
    
})->name('home');

Route::post('/checkout/vnpay', [CheckoutPayment::class, 'payment_vnpay_foradmin']);
Route::post('/checkout/momopay', [CheckoutPayment::class, 'payment_momo']);