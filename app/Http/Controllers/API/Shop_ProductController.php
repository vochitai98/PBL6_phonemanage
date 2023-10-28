<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop_Product;
use Illuminate\Support\Facades\DB;

class Shop_ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop_products = Shop_Product::all();
        return $shop_products;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user();
            $customer_id = $user->id;
            
            $shop_id  = DB::table('shops')
            ->where('customer_id', '=', $customer_id)
            ->get()->first();
            if($shop_id==null){
                return response()->json(['message' => 'Please created shop.'], 401);
            }

            $validatedData= $request->validate([
                //'shop_id' => 'required|exists:shops,id',// Check if it exists in the "shop" table
                'product_id' => 'required|exists:products,id',// Check if it exists in the "product" table
                'price' => 'required|numeric|min:0.01',
                'status' => 'required|boolean',
                'quantity' => 'required|Integer|min:0',
                'warranty' => 'required|Integer|min:0',
    
            ]);
        
            // Create a new record in the "shop_products" table
            $shop_product = Shop_Product::create([
               'shop_id' => $shop_id->id,
               'product_id' => $validatedData['product_id'],
               'price' => $validatedData['price'],
               'status' => $validatedData['status'],
               'quantity' => $validatedData['quantity'], 
               'warranty' =>$validatedData['warranty'], 
               // Set other fields accordingly
            ]);
            return response()->json(['message' => 'resource has been created successfully','data' => $shop_product], 201);
        }else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop_product = Shop_Product::find($id);
        if (!$shop_product) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $shop_product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop_product = Shop_Product::find($id);

        // Check if the brand exists
        if (!$shop_product) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        // Validate the request data
        $validatedData= $request->validate([
            #'shop_id' => 'required|exists:shops,id',// Check if it exists in the "shop" table
            'product_id' => 'required|exists:products,id',// Check if it exists in the "product" table
            'price' => 'required|numeric|min:0.01',
            'status' => 'required|boolean',
            'warranty' => 'required|Integer|min:0',
            'quantity' => 'required|Integer|min:0',

        ]);
        unset($validatedData['shop_id']);
        
        $shop_product->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $shop_product]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop_product = Shop_Product::find($id);
        // Check if the brand exists
        if (!$shop_product) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the brand
        $shop_product->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }

    public function search(Request $request)
    {
        // Lấy thông tin tìm kiếm từ yêu cầu
        $data = $request->input('search');

        $shop_products = DB::table('shop_products')
    ->select('shop_products.*', 'shops.shopName', 'products.name')
    ->join('products', 'products.id', '=', 'shop_products.product_id')
    ->join('shops', 'shops.id', '=', 'shop_products.shop_id')
    ->where('shops.shopName', 'like', "%$data%")
    ->orWhere('products.name', 'like', "%$data%")
    ->get();
    return response()->json($shop_products);
    }

    public function searchByPrice(Request $request)
    {
        // Lấy thông tin tìm kiếm từ yêu cầu
        $maxPrice = (float)$request->input('maxPrice');
        $minPrice = (float)$request->input('minPrice');
        if(!$maxPrice){
            $maxPrice =100000000;
        }
        if(!$minPrice){
            $minPrice=0;
        }
        $shop_products = DB::table('shop_products')
        ->select('products.name','shop_products.price','shop_products.quantity', 'shops.shopName', )
        ->join('products', 'products.id', '=', 'shop_products.product_id')
        ->join('shops', 'shops.id', '=', 'shop_products.shop_id')
        ->where('shop_products.price','>=',$minPrice)
        ->where('shop_products.price','<=',$maxPrice)
        ->orderBy('shop_products.price')
        ->get();
        return response()->json($shop_products);
    }
}
