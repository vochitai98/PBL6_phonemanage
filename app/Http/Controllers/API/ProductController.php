<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return $products;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'seoTitle' => 'required|string|max:255',
                'color' => 'nullable|string|max:255',
                'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
                //'listImage' => 'required|array', // Kiểm tra listImage là một mảng
                'listImage.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra từng phần tử của danh sách là ảnh hợp lệ
                'forwardCameras' => 'required|string|max:255',
                'backwardCameras' => 'required|string|max:255',
                'isNew' => 'required|boolean|max:255',
                'memoryStorage' => 'required|string|max:255',
                'VAT' => 'required|numeric|min:0',
                'status' => 'required|boolean',
                'screen' => 'required|string|max:255',
                'isTrending' => 'nullable|boolean',
                'detail' => 'nullable|string|max:255',
                //'starRated' => 'nullable|integer|min:1|max:5',
                //'viewCount' => 'nullable|integer|',

                'brand_id' => 'required|exists:brands,id',
                'metaKeywords' => 'required|string|max:255',
                'metaDescriptions' => 'required|string|max:255',

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        // Lưu ảnh vào thư mục storage/app/public/images
        $imagePath = $request->file('image')->store('public/images');

        // Tạo đường dẫn URL cho ảnh
        $imageUrl = Storage::url($imagePath);
        // Create a new record in the "shop" table
        $product = Product::create([
            'name' => $validatedData['name'],
            'seoTitle' => $validatedData['seoTitle'],
            'color' => $validatedData['color'],
            'image' => $imageUrl,
            //'listImage' => $validatedData['listImage'],
            'forwardCameras' => $validatedData['forwardCameras'],
            'backwardCameras' => $validatedData['backwardCameras'],
            'isNew' => $validatedData['isNew'],
            'memoryStorage' => $validatedData['memoryStorage'],
            'VAT' => $validatedData['VAT'],
            'status' => $validatedData['status'],
            'screen' => $validatedData['screen'],
            'isTrending' => $validatedData['isTrending'],
            'detail' => $validatedData['detail'],
            //'starRated' => $validatedData['starRated'],
            //'viewCount' => $validatedData['viewCount'],
            'brand_id' => $validatedData['brand_id'],
            'metaKeywords' => $validatedData['metaKeywords'],
            'metaDescriptions' => $validatedData['metaDescriptions'],
            // Set other fields accordingly
        ]);
        return response()->json([
            'message' => 'Product has been created successfully',
            'data' => [
                'product' => $product,
                'image_url' => asset($product->image),
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $product;
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        // Check if the brand exists
        if (!$product) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        try {

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'seoTitle' => 'required|string|max:255',
                'color' => 'nullable|string|max:255',
                'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
                //'listImage' => 'required|array', // Kiểm tra listImage là một mảng
                //'listImage.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra từng phần tử của danh sách là ảnh hợp lệ
                'forwardCameras' => 'required|string|max:255',
                'backwardCameras' => 'required|string|max:255',
                'isNew' => 'required|boolean|max:255',
                'memoryStorage' => 'required|string|max:255',
                'VAT' => 'required|integer|max:255',
                'status' => 'required|boolean',
                'screen' => 'required|string|max:255',
                'isTrending' => 'nullable|boolean',
                'detail' => 'nullable|string|max:255',
                'starRated' => 'nullable|integer|min:1|max:5',
                'viewCount' => 'nullable|integer|',

                'brand_id' => 'required|exists:brands,id',
                'metaKeywords' => 'required|string|max:255',
                'metaDescriptions' => 'required|string|max:255',

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }
        // Validate the request data

        dd($validatedData);
        // Update the customer with the validated data
        $product->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $product]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the product
        $product->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
    public function search(Request $request)
    {
        // Lấy thông tin tìm kiếm từ yêu cầu
        $data = $request->input('search');

        $products = Product::where('name', 'like', '%' . $data . '%')
            ->get();
        return response()->json($products);
    }
}
