<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use App\Models\Shop_Product;
use Laravel\Prompts\Prompt;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = Promotion::all();
        return $promotions;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        if (auth()->guard('customer-api')->check()) {
            $customer_id = auth()->guard('customer-api')->user()->id; //get user currentlty 
            try {
                $validatedData = $request->validate([
                    'name' => 'required|string|max:255',
                    'shop_product_id' => 'required|exists:shop_products,id',
                    'promotionPercentage' => 'nullable|integer|max:100',
                    'promotionReduction' => 'nullable|integer|max:100',
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


            $shop_id = Shop_Product::find($validatedData['shop_product_id'])->shop_id;
            $cus_id = Shop::find($shop_id)->customer_id;

            if ($customer_id != $cus_id) {
                return response()->json(['message' => 'You are not owner shop_product!'], 401);
            }
            // Create a new resource instance
            $promotion = Promotion::create([
                'name' => $validatedData['name'],
                'shop_product_id' => $validatedData['shop_product_id'],
                'promotionPercentage' => $validatedData['promotionPercentage'],
                'promotionReduction' => $validatedData['promotionReduction'],
                'detail' => $validatedData['detail'],
                'status' => $validatedData['status'],
                'quantity' => $validatedData['quantity'],
                'startDate' => $validatedData['startDate'],
                'endDate' => $validatedData['endDate'],
            ]);

            // Return a JSON response indicating success
            return response()->json(['message' => 'Promotion created successfully', 'data' => $promotion], 201);
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
        $shop_product_id = $promotion->shop_product_id;
        $shop_id = Shop_Product::find($shop_product_id)->shop_id;
        $cus_id = Shop::find($shop_id)->customer_id;
        if (auth()->guard('customer-api')->check()) {
            $customer_id = auth()->guard('customer-api')->user()->id;
            if ($cus_id == $customer_id) {
                try {
                    $validatedData = $request->validate([
                        'name' => 'required|string|max:255',
                        //'shop_product_id' => 'required|exists:products,id',
                        'promotionPercentage' => 'nullable|integer|min:1|max:100',
                        'promotionReduction' => 'nullable|integer|min:1|max:100',
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
            $shop_id  = DB::table('shops')
                ->where('customer_id', '=', $customer_id)
                ->get()->first()->id;
            $shop_product_id  = DB::table('shop_products')
                ->where('shop_id', '=', $shop_id)
                ->get()->first()->id;
            $promotions = DB::table('promotions')
                ->where('shop_Product_id', '=', $shop_product_id)
                ->get();
            return response()->json($promotions);
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
    }
}
