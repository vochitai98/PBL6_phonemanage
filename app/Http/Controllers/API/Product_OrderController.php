<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product_Order;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\Shop_Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Product_OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product_orders = Product_Order::take(20)->get();
        return $product_orders;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'shop_product_id' => 'required|exists:shops,id', // Check if it exists in the "shop_product" table
                'order_id' => 'required|exists:orders,id', // Check if it exists in the "order" table
                'quantity' => 'required|integer|min:1',
                'total' => 'required|numeric|min:0',
                'isNew' => 'required|boolean|max:255',

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }



        $product_order = Product_Order::create([
            'shop_product_id' => $validatedData['shop_product_id'],
            'order_id' => $validatedData['order_id'],
            'quantity' => $validatedData['quantity'],
            'total' => $validatedData['total'],
        ]);

        return response()->json(['message' => 'resource has been created successfully', 'data' => $product_order], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product_order = Product_Order::find($id);
        if (!$product_order) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $product_order;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product_order = Product_Order::find($id);

        // Check if the brand exists
        if (!$product_order) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        try {
            // Validate the request data
            $validatedData = $request->validate([
                // 'shop_product_id' => 'required|exists:shops,id',// Check if it exists in the "shop_product" table
                // 'order_id' => 'required|exists:orders,id',// Check if it exists in the "order" table
                'quantity' => 'required|integer|min:1',

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        unset($validatedData['shop_product_id']);
        unset($validatedData['order_id']);
        $product_order->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $product_order]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product_order = Product_Order::find($id);
        // Check if the order exists
        if (!$product_order) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the order
        $product_order->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
    //Add to cart
    public function addToCart(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!']);
        }
        // Validate request data
        try {
            $validatedData = $request->validate([
                'shop_product_id' => 'required|exists:shop_products,id',
                'quantity' => 'required|integer|min:1',
                'order_id' => 'nullable'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        $quantity_product = Shop_Product::find($validatedData['shop_product_id'])->quantity;
        // Assuming you have an authenticated user
        $user_id = auth()->guard('customer-api')->id();

        // Find the user's open order or create a new one
        $order = Order::firstOrCreate([
            'customer_id' => $user_id,
            'status' => 'cart', // or any other status indicating a cart
        ]);

        // Check if the product is already in the cart, update quantity if true
        $existingShopProduct = DB::table('product_orders')
            ->where('shop_product_id', $request->input('shop_product_id'))
            ->where('order_id', $order->id)
            ->first();

        if ($existingShopProduct) {
            //dd($existingShopProduct);
            $record = Product_Order::find($existingShopProduct->id);
            $quantity = $record->quantity + $request->input('quantity');
            if ($quantity > $quantity_product) {
                return response()->json(['message' => 'The quantity of items in stock is not enough!']);
            }
            $record->update(['quantity' => $quantity]);
            $product_order = $record;
        } else {
            if ($validatedData['quantity'] > $quantity_product) {
                return response()->json(['message' => 'The quantity of items in stock is not enough!']);
            }
            $product_order = Product_Order::create([
                'shop_product_id' => $validatedData['shop_product_id'],
                'order_id' => $order->id,
                'quantity' => $validatedData['quantity'],
            ]);
            // Otherwise, attach the product to the cart with quantity
        }

        return response()->json(['message' => 'Product added to cart successfully', 'data' => $product_order]);
    }

    //view cart
    public function viewCart()
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!']);
        }
        $order = DB::table('orders')
            ->select('*')
            ->where('orders.customer_id', auth()->guard('customer-api')->id())
            ->Where('orders.status', 'cart')
            ->first();
        $product_orders = DB::table('product_orders')
            ->select('product_orders.id', 'products.name', 'products.image','shop_products.id as shop_product_id', 'shop_products.price', 'product_orders.quantity as quantity_order', 'shop_products.quantity as quantity_product', 'shop_products.shop_id as shop_id', 'shops.shopName')
            ->join('shop_products', 'product_orders.shop_product_id', '=', 'shop_products.id')
            ->join('products', 'products.id', '=', 'shop_products.product_id')
            ->join('shops', 'shops.id', '=', 'shop_products.shop_id')
            ->where('product_orders.order_id', $order->id)
            ->get();

        return response()->json(['data' => $product_orders]);
    }

    public function in_decreaseAmount(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!']);
        }
        $id = $request->product_order_id;
        $product_order = Product_Order::find($id);
        $quantity_product = Shop_product::find($product_order->shop_product_id)->quantity;
        if ($request->has('increase')) {
            $product_order->quantity += 1;
            if ($quantity_product < $product_order->quantity) {
                return response()->json(['message' => 'Sorry, the product is temporarily out of stock!']);
            }
        } else {
            $product_order->quantity -= 1;
            if ($product_order->quantity == 0) {
                $product_order->delete();
                return response()->json(['message' => 'Deleted sussecefull!']);
            }
        }
        $product_order->save();
        return response()->json(['data' => $product_order]);
    }

    //orrder
    public function handleOderred(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!']);
        }
        try {
            // Validate the request data
            $validatedData = $request->validate([
                //'customer_id' => 'required|exists:customers,id',
                'discount' => 'nullable|Integer|min:0',
                'shop_id' => 'nullable',
                'discount_amount' => 'nullable|Integer|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }


        //get input FE
        $data = $request->input('data');
        $paid = $request->input('paid');
        $codeDiscountApp = $request->input('code_discount_app');
        //$discount_amount = $request->input('discount_amount');
        // Test
        $allDiscountCodes = [];

        foreach ($data as $item) {
            // Tách các giá trị từ trường code_discount
            $discountCodes = explode(',', $item['code_discount']);
            
            // Lặp qua mỗi giá trị và thêm vào mảng nếu chưa tồn tại
            foreach ($discountCodes as $code) {
                
                $code = trim($code); // Loại bỏ khoảng trắng nếu có
                if (!empty($code) && !in_array($code, $allDiscountCodes)) {
                    $allDiscountCodes[] = $code;
                }
            }
        }
        // Kiểm tra và thêm mã giảm giá từ trường "code_discount_app" vào danh sách
        $codeDiscountAppArray = explode(',', $codeDiscountApp);
        foreach ($codeDiscountAppArray as $code) {
            $code = trim($code);
            if (!empty($code) && !in_array($code, $allDiscountCodes)) {
                $allDiscountCodes[] = $code;
            }
        }
        // Nếu allDiscountCodes rỗng và code_discount_app không rỗng, thêm code_discount_app vào allDiscountCodes
        if (empty($allDiscountCodes) && !empty($codeDiscountApp)) {
            $allDiscountCodes[] = $codeDiscountApp;
        }
        if (!empty($allDiscountCodes)) {
            foreach ($allDiscountCodes as $all) {
                
                $promotion = Promotion::where('code', $all)->first();
                if($promotion==null){
                    return response()->json(['message' => 'Mã khuyến mãi không tồn  tại!'], 422);
                }
                $promotion->quantity -= 1;
                $promotion->save();
            }
        }
        // $allDiscountCodes chứa tất cả các mã giảm giá duy nhất


        foreach ($data as $dt) {
            
            $shop_id = $this->getShopIdByProductOrderId($dt['product_order_id']);
            $order = Order::create([
                'customer_id' => auth()->guard('customer-api')->id(),
                'status' => 'pending',
                'paid' => $paid,
                'discount' => $dt['code_discount'],
                'discount_amount' => $dt['discount_amount'],
                'shop_id' => $shop_id,
                'total_price' => $dt['total_price'],

            ]);
            $product_order = Product_Order::find($dt['product_order_id']);
            $product_order->order_id = $order->id;
            $product_order->save();
        }

        foreach ($data as $dt) {
            $quantity_product_order = Product_Order::find($dt['product_order_id'])->quantity;
            $shop_product_id = Product_Order::find($dt['product_order_id'])->shop_product_id;
            $shop_product = Shop_product::find($shop_product_id);
            $shop_product->quantity =  $shop_product->quantity - $quantity_product_order;
            $shop_product->save();
        }


        // $product_items = DB::table('product_orders')
        //     ->select('product_orders.id','products.name','shop_products.price','product_orders.quantity as quantity_order','shop_products.id as shop_product_id')
        //     ->join('shop_products', 'product_orders.shop_product_id', '=', 'shop_products.id')
        //     ->join('products', 'products.id', '=', 'shop_products.product_id')
        //     ->where('product_orders.order_id',$order->id)
        //     ->get();
        // foreach($product_items as $product_item){
        //     $sop = Shop_Product::find($product_item->shop_product_id);
        //     $sop->quantity -= $product_item->quantity_order;
        //     $sop->save();
        // }

        return response()->json('ordered successful!', 200);
    }
    //get shop_id by product_order_id
    public function getShopIdByProductOrderId($product_order_id)
    {
        $shop_id = DB::table('product_orders')
            ->select('shop_products.shop_id')
            ->join('shop_products', 'product_orders.shop_product_id', '=', 'shop_products.id')
            ->where('product_orders.id', '=', $product_order_id)
            ->get(1);
        // $shop_product_id=Product_Order::find($product_order_id)->shop_product_id;
        // $shop_id=Shop_Product::find($shop_product_id)->shop_id;
        return $shop_id[0]->shop_id;
    }
    // public function checkOrderByProductOrrder_id($product_order_id,$customer_id){
    //     $order=DB::table('orders')
    //         ->select('orders.id')
    //         ->where('customer_id','=',$customer_id)
    //         ->where('customer_id','=',$customer_id)
    //         ->get(1);
    // }

    //shop ban hang
    public function delete_cart(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!']);
        }
        try {
            // Validate the request data
            $validatedData = $request->validate([
                //'customer_id' => 'required|exists:customers,id',
                'discount' => 'nullable|Integer|min:0',
                'shop_id' => 'nullable'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }


        //get input FE
        $product_order_ids = $request->input('product_order_ids');

        foreach ($product_order_ids as $product_order_id) {
            $product_order = Product_Order::find($product_order_id);
            // Check if the order exists
            if (!$product_order) {
                return response()->json(['message' => 'Resource not found'], 404);
            }
            // Delete the order
            $product_order->delete();
        }

        return response()->json(['message' => 'Deleted successful!'], 200);
    }
    public function addCodePromotionByShop(Request $request)
    {
        if (!auth()->guard('customer-api')->check()) {
            return response()->json(['message' => 'You are not loged in!']);
        }
        $isShop = $request->input('isShop');
        dd($isShop);
        if($isShop !== null){
            $ngayHienTai = Carbon::now()->format('Y-m-d');
        $shop_prodcut_id = $request->input('shop_product_id');
        $shop_id = Shop_Product::find($shop_prodcut_id)->shop_id;
        $shopPromotions = DB::table('promotions')
            ->select('promotions.*','shops.shopName','products.name')
            ->join('shop_products', 'promotions.shop_product_id', '=', 'shop_products.id')
            ->join('shops', 'shops.id', '=', 'shop_products.shop_id')
            ->join('products', 'products.id', '=', 'shop_products.product_id')
            ->where('shop_product_id',$shop_prodcut_id)
            ->where('promotions.quantity', '>',0)
            ->get();

        $shop_productPromotions = Promotion::where('shop_id', $shop_id)->get()->groupBy('product_code');

        return response()->json([
            'shop_promotions' => $shopPromotions,
            'shop_productPromotions' => $shop_productPromotions,
        ]);
        }
        $ngayHienTai = Carbon::now()->format('Y-m-d');
        $shop_prodcut_id = $request->input('shop_product_id');
        $shop_id = Shop_Product::find($shop_prodcut_id)->shop_id;
        $shopPromotions = DB::table('promotions')
            ->select('promotions.*','shops.shopName','products.name')
            ->join('shop_products', 'promotions.shop_product_id', '=', 'shop_products.id')
            ->join('shops', 'shops.id', '=', 'shop_products.shop_id')
            ->join('products', 'products.id', '=', 'shop_products.product_id')
            ->where('shop_product_id',$shop_prodcut_id)
            ->where('promotions.quantity', '>',0)
            ->whereDate('promotions.startDate', '<=', $ngayHienTai)
            ->whereDate('promotions.endDate', '>=', $ngayHienTai)
            ->get();

        $shop_productPromotions = Promotion::where('shop_id', $shop_id)->get()->groupBy('product_code');

        return response()->json([
            'shop_promotions' => $shopPromotions,
            'shop_productPromotions' => $shop_productPromotions,
        ]);
    }
    public function addCodePromotionbyApp_Wed(Request $request)
    {
        // if (!auth()->guard('customer-api')->check()) {
        //     return response()->json(['message' => 'You are not loged in!']);
        // }
        $ngayHienTai = Carbon::now()->format('Y-m-d');
        //dd($ngayHienTai);
        $order = DB::table('promotions')
            ->select('*')
            ->where('shop_product_id', '=', null)
            ->where('shop_id', '=', null)
            ->where('quantity', '>',0)
            ->whereDate('startDate', '<=', $ngayHienTai)
            ->whereDate('endDate', '>=', $ngayHienTai)
            ->get();
        return response()->json(['data' => $order], 200);
    }
}
