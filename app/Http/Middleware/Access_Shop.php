<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Access_Shop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('customer-api')->check()) {
            $id_shop = $request->route('id');
            $shop = Shop::find($id_shop);
            if($shop==null){
                return response()->json(['message' => 'Resource not found'], 403);
            }
            if(auth()->guard('customer-api')->id() == $shop->customer_id){
                return $next($request);
            }
            return response()->json(['message' => 'You are not shop owner!'], 403);
        }
            return response()->json(['message' => 'You are not loged in!'], 403);
    }
}
