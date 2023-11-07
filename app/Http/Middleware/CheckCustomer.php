<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {  
        if (auth()->guard('customer-api')->check()) {
            if(auth()->guard('customer-api')->id()== $request->route('id')){
            return $next($request);
            }
            return response()->json(['message' => 'You are not author!'], 403);

        }
        
            // Đây là người dùng từ bảng `customer`
        return response()->json(['message' => 'You are not Customers'], 403);

        
    }
}
