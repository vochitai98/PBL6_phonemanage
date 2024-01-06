<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\API\Carbon;

class CustomerController extends Controller
{
    public function index()
    {
        //
        $customers = Customer::take(20)->get();
        return $customers;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:15|unique:customers,phone',
                'password' => 'required|string|min:6|max:255',
                'address' => 'nullable|string|max:255',
                'sex' => 'nullable|boolean',
                'accumulatedPoint' => 'nullable|integer',
                'dayOfBirth' => 'nullable|date',
                // Add validation rules for other fields
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        // Create a new resource instance
        $customer = Customer::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => $validatedData['password'],
            'address' => $validatedData['address'],
            'sex' => $validatedData['sex'],
            'accumulatedPoint' => $validatedData['accumulatedPoint'],
            'dayOfBirth' => $validatedData['dayOfBirth'],
            // Set other fields accordingly
        ]);

        // Return a JSON response indicating success
        return response()->json(['message' => 'Customer created successfully', 'data' => $customer], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $customer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user();
            $customer_id = $user->id;
        }else{
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
        // Find the resource by its ID
        $customer = Customer::find($customer_id);
        
        // Check if the brand exists
        if (!$customer) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:15',
                //'password' => 'required|string|min:6|max:255'
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Đảm bảo đây là một ảnh hợp lệ
                'address' => 'nullable|string|max:255',
                'sex' => 'nullable|boolean',
                'accumulatedPoint' => 'nullable|integer',
                'dayOfBirth' => 'nullable|date',
                // Add validation rules for other fields
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        if ($request->hasFile('avatar')) {
            // Lưu trữ ảnh vào thư mục public/images và nhận đường dẫn lưu trữ
            $imagePath = $request->file('avatar')->store('public/images');
    
            // Tạo đường dẫn URL cho ảnh
            $imageUrl = Storage::url($imagePath);
            $imageUrl = str_replace('/storage', 'storage', Storage::url($imagePath));
            // Thêm đường dẫn ảnh vào dữ liệu được cập nhật
            $validatedData['avatar'] = $imageUrl;
        }
        // Update the customer with the validated data
        $customer->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $customer]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);
        // Check if the brand exists
        if (!$customer) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the brand
        $customer->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
    public function search(Request $request)
    {
        // Lấy thông tin tìm kiếm từ yêu cầu
        $data = $request->input('search');

        // Thực hiện tìm kiếm trong cơ sở dữ liệu
        $customers = Customer::where('name', 'like', '%' . $data . '%')
            ->orWhere('email', 'like', '%' . $data . '%')
            ->orWhere('phone', 'like', '%' . $data . '%')
            ->get();
        return response()->json($customers);
    }
    public function changePassword(Request $request)
    {
        
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6',
                'confirm_password' => 'required|same:new_password',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user();
            $customer_id = $user->id;
        }else{
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
        // Find the resource by its ID
        $user = Customer::find($customer_id);
        

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['error' => 'Mật khẩu hiện tại không chính xác'], 401);
        }

       // Update the password
    $user->password = Hash::make($request->input('new_password'));
    $user->save();

    return response()->json(['message' => 'Đổi mật khẩu thành công'], 200);
    }

    public function register(Request $request)
    {
        // Validate the incoming request data
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:15|unique:customers,phone',
                'password' => 'required|string|min:6|max:255'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }
        try {
            $validatedData1 = $request->validate([
                'shopName' => 'nullable|string|max:255',
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

        $birthdate = new \DateTime();
        $birthdate->modify('-18 years');
        $birthdateFormatted = $birthdate->format('Y-m-d');
        $validatedDayOfBirth= $birthdateFormatted;
        $validatedImage = "storage/images/UOVPRh3gaGFxWdeD9wKbX6ejhzFeg4SvUD31aGUU.png";
        $customer = Customer::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => Hash::make($validatedData['password']),
            'avatar' => $validatedImage,
            'dayOfBirth' => $validatedDayOfBirth,
        ]);
        $shopName = "";
        $shop = Shop::create([
            'shopName' => $shopName,
            'customer_id'=> $customer->id
            // Set other fields accordingly
        ]);
        // Optionally, you can generate an access token for the registered customer
        $token = $customer->createToken('authToken')->accessToken;
        return response()->json(['customer' => $customer, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->guard('customer-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'customer_id' =>  auth()->guard('customer-api')->id(),
            //'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    public function me()
    {
        return response()->json(auth()->guard('customer-api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerProfile()
    {
        if(!auth()->guard('customer-api')->check()){
            return response()->json(['message' => 'You are not loged in!']);
        }
        $customer = Customer::find(auth()->guard('customer-api')->id());
        if (!$customer) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $customer;
    }

    public function customerOrders(Request $request)
    {
        if(!auth()->guard('customer-api')->check()){
            return response()->json(['message' => 'You are not loged in!']);
        }
        $customer_id = auth()->guard('customer-api')->id();
        $status = $request->input('status');
        if($status){
            $orders = DB::table('orders')
                ->select('orders.id as order_id','orders.shop_id','shops.shopName','orders.total_price','orders.customer_id','shop_products.id as shop_product_id','products.name','products.image')
                ->join('product_orders', 'product_orders.order_id', '=', 'orders.id')
                ->Join('shop_products', 'shop_products.id', '=', 'product_orders.shop_product_id')
                ->Join('shops', 'shops.id', '=', 'shop_products.shop_id')
                ->Join('products', 'products.id', '=', 'shop_products.product_id')
                ->where('orders.customer_id','=',$customer_id)
                ->where('orders.status','=',$status)
                ->take(20)
                ->get();
        }else{
                $orders = DB::table('orders')
                ->select('orders.id as order_id','orders.shop_id','shops.shopName','orders.total_price','orders.customer_id','orders.status','shop_products.id as shop_product_id','products.name','products.image')
                ->join('product_orders', 'product_orders.order_id', '=', 'orders.id')
                ->Join('shop_products', 'shop_products.id', '=', 'product_orders.shop_product_id')
                ->Join('shops', 'shops.id', '=', 'shop_products.shop_id')
                ->Join('products', 'products.id', '=', 'shop_products.product_id')
                ->where('orders.customer_id','=',$customer_id)
                ->where('orders.status','!=','cart')
                ->take(20)
                ->get();
        }
        return $orders;
    }
}
