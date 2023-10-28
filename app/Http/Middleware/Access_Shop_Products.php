<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class Access_Shop_Products
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('customer-api')->check()) {
            $id_shop_product = $request->route('id');
            $shop_product = DB::table('shop_products')->find($id_shop_product);
            if($shop_product==null){
                return response()->json(['message' => 'Resource not found'], 403);
            }
            $user_id = auth()->guard('customer-api')->id();
            $shop_id  = DB::table('shops')
            ->where('customer_id', '=', $user_id)
            ->get()->first()->id;
            
            if($shop_id == $shop_product->shop_id){
            return $next($request);
            }
            return response()->json(['message' => 'You are not shop owner!'], 403);
        }
        
            // Đây là người dùng từ bảng `customer`
        return response()->json(['message' => 'You are not loged in!'], 403);
    }
}
