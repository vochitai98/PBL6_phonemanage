<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product_Order;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Shop_Product;
use Illuminate\Support\Facades\DB;

use function Psy\sh;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shops = Shop::take(20)->get();
        return $shops;
    }

    /**
     * Show the form for creating a new resource.
     */

    public function store(Request $request)
    {
        // Validate the input data (you can add more validation rules if needed)
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user(); //get user currentlty
            $customer_id = $user->id; // get id user
            try {
                $validatedData = $request->validate([
                    'shopName' => 'required|string|max:255',
                    'shopAddress' => 'nullable|string|max:255',
                    'shopPhone' => 'nullable|string|max:255',
                    'state' => 'nullable|boolean|',
                    'bankAccount' => 'nullable|string|max:30',
                    //'customer_id' => 'required|exists:customers,id',
                    'vnp_TmnCode' => 'nullable|string|max:30',
                    'vnp_HashSecret' => 'nullable|string|max:60',

                    // Check if it exists in the "customers" table
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Handle validation errors
                return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
            }
            // Create a new record in the "shop" table
            $shop = Shop::create([
                'shopName' => $validatedData['shopName'],
                'shopAddress' => $validatedData['shopAddress'],
                'shopPhone' => $validatedData['shopPhone'],
                'state' => $validatedData['state'],
                'bankAccount' => $validatedData['bankAccount'],
                'customer_id' => $customer_id,
                'vnp_TmnCode' => $validatedData['vnp_TmnCode'],
                'vnp_HashSecret' => $validatedData['vnp_HashSecret'],
                // Set other fields accordingly
            ]);
            return response()->json(['message' => 'Shop has been created successfully', 'data' => $shop], 201);
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $shop;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = Shop::find($id);

        // Check if the shop exists
        if (!$shop) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'shopName' => 'required|string|max:255',
                'shopAddress' => 'nullable|string|max:255',
                'shopPhone' => 'nullable|string|max:255',
                'state' => 'nullable|boolean',
                'bankAccount' => 'nullable|string|max:30',
                'vnp_TmnCode' => 'nullable|string|max:30',
                'vnp_HashSecret' => 'nullable|string|max:60',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        // Validate the request data

        unset($validatedData['customer_id']);
        // Update the customer with the validated data
        $shop->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $shop]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop = Shop::find($id);
        // Check if the brand exists
        if (!$shop) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the brand
        $shop->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }

    public function search(Request $request)
    {
        // Lấy thông tin tìm kiếm từ yêu cầu
        $data = $request->input('search');

        $shops = Shop::where('shopName', 'like', '%' . $data . '%')
            ->orWhere('shopAddress', 'like', '%' . $data . '%')
            ->orWhere('shopPhone', 'like', '%' . $data . '%')
            ->get();
        return response()->json($shops);
    }
    //Get shop by Customer_id
    public function getShopByCustomerId(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!'], 404);
        }
        $customer_id = auth()->guard('customer-api')->id();
    $shop = Shop::where('customer_id', $customer_id)->first();
        if ($shop) {
            return response()->json(['data' => $shop], 200);
        } else {
            return response()->json(['message' => 'You have not created a store yet'], 404);
        }
    }
    //lấy danh sách sản phẩm mà shop bán
    public function getListShopProductByCustomerId(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!'], 404);
        }
        $customer_id = auth()->guard('customer-api')->id();
        $shop = Shop::where('customer_id', $customer_id)->first();

        if ($shop) {
            $shop_products = DB::table('shop_products')
                ->select('shop_products.*', 'products.image', 'products.name')
                ->join('products', 'shop_products.product_id', '=', 'products.id')
                ->where('shop_id', '=', $shop->id)
                ->take(20)
                ->get();
            return response()->json(['data' => $shop_products], 200);
        } else {
            return response()->json(['message' => 'You have not created a store yet'], 404);
        }
    }
    //lấy tất cả đơn hàng
    public function getAllOrderByCustomerID(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!'], 404);
        }
        $customer_id = auth()->guard('customer-api')->id();
        $shop = Shop::where('customer_id', $customer_id)->first();
        $status = $request->input('status');
        if ($shop) {
            if ($status != "all") {
                $orders = DB::table('orders')
                    ->select('orders.*', 'customers.name as customer_name', 'products.name', 'products.image')
                    ->join('customers', 'customers.id', '=', 'orders.customer_id')
                    ->join('product_orders', 'orders.id', '=', 'product_orders.order_id')
                    ->join('shop_products', 'product_orders.shop_product_id', '=', 'shop_products.id')
                    ->join('products', 'shop_products.product_id', '=', 'products.id')
                    ->where('orders.shop_id', '=', $shop->id)
                    ->where('orders.status', '=', $status)
                    ->take(20)
                    ->get();
            } else {
                $orders = DB::table('orders')
                    ->select('orders.*', 'customers.name as customer_name', 'products.name', 'products.image')
                    ->join('customers', 'customers.id', '=', 'orders.customer_id')
                    ->join('product_orders', 'orders.id', '=', 'product_orders.order_id')
                    ->join('shop_products', 'product_orders.shop_product_id', '=', 'shop_products.id')
                    ->join('products', 'shop_products.product_id', '=', 'products.id')
                    ->where('orders.shop_id', '=', $shop->id)
                    ->where('orders.status', '!=', 'cart')
                    ->take(20)
                    ->get();
            }
            return response()->json(['data' => $orders], 200);
        } else {
            return response()->json(['message' => 'You have not created a store yet'], 404);
        }
    }
    public function shopProcessesOrders(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!'], 404);
        }
        $customer_id = auth()->guard('customer-api')->id();
        $shop = Shop::where('customer_id', $customer_id)->first(); //login shop
        $order_id = $request->input('order_id');
        $product_order = DB::table('product_orders')
            ->where('order_id', '=', $order_id)->first();
        $product_order_quantity = $product_order->quantity;
        $shop_product_id = $product_order->shop_product_id;
        $shop_product = Shop_Product::find($shop_product_id);
        $shop_order = Order::find($order_id); //id of  order_shop
        if ($shop->id != $shop_order->shop_id) {
            return response()->json(['message' => 'You are not owner shop!'], 404);
        }
        $status = $request->input('status');
        if ($status == "Canceled") {
            $shop_product->quantity = $shop_product->quantity + $product_order_quantity;
            $shop_product->save();
        }
        $shop_order->status = $status;
        $shop_order->save();
        return response()->json(['data' => $shop_order], 200);
    }
    //thong ke doanh thu
    public function revenueStatistics(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!'], 404);
        }
        $customer_id = auth()->guard('customer-api')->id();
        $shop = Shop::where('customer_id', $customer_id)->first(); //login shop
        $shopId = $shop->id; // Thay thế 23 bằng shop_id bạn có
        $results = DB::table('orders')
            ->select(DB::raw('DATE(updated_at) as order_date'), DB::raw('SUM(total_price) as total_revenue'))
            ->where('status', 'completed')
            ->where('shop_id', $shopId)
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->get();
        return response()->json(['data' => $results], 200);
    }
    public function purchasePriceRangeStatistics(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!'], 404);
        }
        $customer_id = auth()->guard('customer-api')->id();
        $shop = Shop::where('customer_id', $customer_id)->first(); //login shop
        $shopId = $shop->id; // Thay thế 23 bằng shop_id bạn có

        $ranges = ['0-10000000', '10000000-20000000', '20000000-30000000', '30000000-40000000', '40000000-50000000', '50000000-60000000', '60000000-1000000000'];
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');


        $results = collect($ranges)->flatMap(function ($range) use ($shopId, $startDate, $endDate) {
            list($minPrice, $maxPrice) = explode('-', $range);

            $result = DB::table('orders')
                ->select('shop_id', DB::raw('COUNT(*) as order_count'))
                ->selectRaw('CASE WHEN total_price BETWEEN ? AND ? THEN ? ELSE "Others" END AS `range`', [$minPrice, $maxPrice, $range])
                ->where('status', 'completed')
                ->where('shop_id', $shopId)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->whereBetween('total_price', [$minPrice, $maxPrice])
                ->groupBy('shop_id', 'range')
                ->get();

            // Nếu không có đơn hàng nằm trong phạm vi này, thêm một kết quả với order_count = 0
            if ($result->isEmpty()) {
                $result = collect([
                    (object) [
                        'shop_id' => $shopId,
                        'order_count' => 0,
                        'range' => $range,
                    ],
                ]);
            }

            return $result;
        });

        return response()->json(['data' => $results], 200);
    }
    public function allShopRevenueStatistics(Request $request)
    {
        if (!auth()->guard('admin-api')->check()) {
            return response()->json(['message' => 'You are not admin!'], 404);
        }
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $result =DB::table('orders')
            ->select('shops.id','shops.shopName', DB::raw('SUM(orders.total_price) as total_revenue'))
            ->join('shops', 'orders.shop_id', '=', 'shops.id')
            ->whereBetween('orders.updated_at',[$startDate, $endDate])
            ->whereNotNull('orders.shop_id')
            ->groupBy('shops.id', 'shops.shopName')
            ->get();

        return $result;
    }
}
