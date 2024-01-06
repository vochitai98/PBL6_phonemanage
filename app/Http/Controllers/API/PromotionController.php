<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use App\Models\Shop_Product;
use Carbon\Carbon;
use Laravel\Prompts\Prompt;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = Promotion::where('quantity','>',0)->get();
        return $promotions;
    }


    /**
     * Store a newly created resource in storage.
     */
    public static function randul()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < 6; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
    public function store(Request $request)
    {
        // Validate the incoming request data

        $code=$this->randul();

        
        try {
            $validatedData = $request->validate([
                //'code' => 'required|string|max:255',
                'shop_product_id' => 'nullable|exists:shop_products,id',
                //'shop_id' => 'nullable|integer',
                'type' => 'nullable|boolean',
                'value' => 'nullable|integer|min:0',
                'minPriceCondition' => 'nullable|integer|min:0',
                'detail' => 'required|string|max:255',
                'status' => 'required|boolean',
                'quantity' => 'required|integer',
                'startDate' => 'nullable|date',
                'endDate' => 'nullable|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }
        if (auth()->guard('customer-api')->check()) {
            $customer_id = auth()->guard('customer-api')->user()->id; //get user currentlty 
            $shop_id = Shop::where('customer_id',$customer_id)->first()->id;
            // try {
            //     $validatedData = $request->validate([
            //         //'code' => 'required|string|max:255',
            //         'shop_product_id' => 'nullable|exists:shop_products,id',
            //         //'shop_id' => 'nullable|integer',
            //         'type' => 'nullable|boolean',
            //         'value' => 'nullable|integer|min:1|max:100',
            //         'detail' => 'required|string|max:255',
            //         'status' => 'required|boolean',
            //         'quantity' => 'required|integer',
            //         'startDate' => 'nullable|date',
            //         'endDate' => 'nullable|date',
            //     ]);
            // } catch (\Illuminate\Validation\ValidationException $e) {
            //     // Handle validation errors
            //     return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
            // }
            if(empty($validatedData['shop_product_id'])){
                $promotion = Promotion::create([
                    'code' => $code,
                    //'shop_product_id' => $validatedData['shop_product_id'],
                    'shop_id' => $shop_id,
                    'type' => $validatedData['type'],
                    'value' => $validatedData['value'],
                    'minPriceCondition' => $validatedData['minPriceCondition'],
                    'detail' => $validatedData['detail'],
                    'status' => $validatedData['status'],
                    'quantity' => $validatedData['quantity'],
                    'startDate' => $validatedData['startDate'],
                    'endDate' => $validatedData['endDate'],
                ]);
                return response()->json(['message' => 'Promotion by all shop_product created successfully', 'data' => $promotion], 201);
            }
            //lấy id chủ shop so sánh xem có phải chủ shop thêm ko
            $sh_id = Shop_Product::find($validatedData['shop_product_id'])->shop_id;
            if ($shop_id != $sh_id) {
                return response()->json(['message' => 'You are not owner shop_product!'], 401);
            }
            
            //tạo cho shop
            $promotion = Promotion::create([
                'code' => $code,
                'shop_product_id' => $validatedData['shop_product_id'],
                //'shop_id' => $shop_id,
                'type' => $validatedData['type'],
                'value' => $validatedData['value'],
                'minPriceCondition' => $validatedData['minPriceCondition'],
                'detail' => $validatedData['detail'],
                'status' => $validatedData['status'],
                'quantity' => $validatedData['quantity'],
                'startDate' => $validatedData['startDate'],
                'endDate' => $validatedData['endDate'],
            ]);

            // Return a JSON response indicating success
            return response()->json(['message' => 'Promotion by shop_product_id created successfully', 'data' => $promotion], 201);
        }
        else if(auth()->guard('admin-api')->check()){
            $promotion = Promotion::create([
                'code' => $code,
                //'shop_product_id' => $validatedData['shop_product_id'],
                //'shop_id' => $shop_id,
                'type' => $validatedData['type'],
                'value' => $validatedData['value'],
                'minPriceCondition' => $validatedData['minPriceCondition'],
                'detail' => $validatedData['detail'],
                'status' => $validatedData['status'],
                'quantity' => $validatedData['quantity'],
                'startDate' => $validatedData['startDate'],
                'endDate' => $validatedData['endDate'],
            ]);

            // Return a JSON response indicating success
            return response()->json(['message' => 'Promotion by admin created successfully', 'data' => $promotion], 201);
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $promotion;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $promotion = Promotion::find($id);
        // Check if the review exists
        if (!$promotion) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // $shop_product_id = $promotion->shop_product_id;
        // $shop_id = Shop_Product::find($shop_product_id)->shop_id;
        //$cus_id = Shop::find($shop_id)->customer_id;
        if (auth()->guard('customer-api')->check()) {
            $customer_id = auth()->guard('customer-api')->user()->id;
            if ($customer_id) {
                try {
                    $validatedData = $request->validate([
                        //'code' => 'required|string|max:255',
                        //'shop_product_id' => 'required|exists:products,id',
                        'type' => 'nullable|boolean',
                        'value' => 'nullable|integer|min:1',
                        'detail' => 'required|string|max:255',
                        'status' => 'required|boolean',
                        'minPriceCondition' => 'nullable|integer',
                        'quantity' => 'required|integer',
                        'startDate' => 'nullable|date',
                        'endDate' => 'nullable|date',
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    // Handle validation errors
                    return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
                }
                unset($validatedData['code']);
                unset($validatedData['shop_id']);
                unset($validatedData['shop_product_id']);
                // Update the promotion with the validated data
                $promotion->update($validatedData);

                return response()->json(['message' => 'Resource updated successfully', 'data' => $promotion]);
            } else {
                return response()->json(['message' => 'You are not owner!'], 401);
            }
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promotion = Promotion::find($id);
        // Check if the promotion exists
        if (!$promotion) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the promotion
        $promotion->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }

    public function getPromotionByIdCutomer(Request $request)
    {
        // Lấy thông tin tìm kiếm từ yêu cầu
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user();
            $customer_id = $user->id;
            $shop_id  = $request->input('shop_id');
            //kiem tra xem là shop hay la customer
            $isShop = $request->input('isShop');

            if(!$request->has('isShop')){
                $ngayHienTai = Carbon::now()->format('Y-m-d');
                $promotions_shop_product_id  = DB::table('promotions')
                    ->select('promotions.*','shops.shopName','products.name')
                    ->join('shop_products', 'promotions.shop_product_id', '=', 'shop_products.id')
                    ->join('shops', 'shops.id', '=', 'shop_products.shop_id')
                    ->join('products', 'products.id', '=', 'shop_products.product_id')
                    ->where('shop_products.shop_id', '=', $shop_id)
                    ->whereDate('promotions.startDate', '<=', $ngayHienTai)
                    ->whereDate('promotions.endDate', '>=', $ngayHienTai)
                    ->get();
                $promotion_shop_id = DB::table('promotions')
                    ->select('promotions.*','shops.shopName')
                    ->join('shops', 'shops.id', '=', 'promotions.shop_id')
                    ->where('shop_id', '=', $shop_id)
                    ->whereDate('promotions.startDate', '<=', $ngayHienTai)
                    ->whereDate('promotions.endDate', '>=', $ngayHienTai)
                    ->get();
                
            return response()->json(['promotions_shop_product_id'=>$promotions_shop_product_id,'promotion_shop_id'=>$promotion_shop_id]);
            }

            $promotions_shop_product_id  = DB::table('promotions')
                ->select('promotions.*','shops.shopName','products.name')
                ->join('shop_products', 'promotions.shop_product_id', '=', 'shop_products.id')
                ->join('shops', 'shops.id', '=', 'shop_products.shop_id')
                ->join('products', 'products.id', '=', 'shop_products.product_id')
                ->where('shop_products.shop_id', '=', $shop_id)
                ->get();
            $promotion_shop_id = DB::table('promotions')
                ->select('promotions.*','shops.shopName')
                ->join('shops', 'shops.id', '=', 'promotions.shop_id')
                ->where('shop_id', '=', $shop_id)
                ->get();
            return response()->json(['promotions_shop_product_id'=>$promotions_shop_product_id,'promotion_shop_id'=>$promotion_shop_id]);
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
    }
    public function AddPromotionIdOfShop(Request $request)
    {
        $id = $request->input('id');
        $promotion = Promotion::find($id);
        // Check if the promotion exists
        if (!$promotion) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the promotion
        $promotion->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
}
