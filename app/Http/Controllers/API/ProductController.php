<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all()->take(12);
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
                'color' => 'required|string|max:255',
                'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
                'listImage' => 'nullable|array', // Kiểm tra listImage là một mảng
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

        $products = Product::where('name', 'like', '%' . $data . '%')->take(12)
            ->get();
        return response()->json($products);
    }

    public function getProductByBrand(Request $request)
    {
        // Lấy thông tin tìm kiếm từ yêu cầu
        $data = $request->input('brand_id');

        $products = Product::where('brand_id', '=',$data)->take(12)
            ->get();
        return response()->json($products);
    }
    //crawl data
    public function importJson(Request $request)
    {
        $jsonFilePath = storage_path('\app\jons\test.json'); // Adjust the path to your JSON file

        // Check if the file exists
        $jsonContent = File::get($jsonFilePath);

        // Chuyển đổi JSON thành mảng
        $dataArray = json_decode($jsonContent, true);
        //dd($dataArray);
        $i = 0;
        
        foreach ($dataArray as $row) {
            $response = Http::get($row['img']);
            $fileName = basename($row['img']);
            $path = 'images/' . $fileName;
            $imagePath= Storage::put('public/'.$path, $response->body());
            $imageUrl = 'storage/'.$path;
            $title = substr($row['title'], 16);
            if (stripos($row['title'], "iphone")) {
                $row['brand']= 1;
            } 
            else if(stripos($row['title'], "oppo")){
                $row['brand']= 2;
            }
            else if(stripos($row['title'], "vivo")){
                $row['brand']= 4;
            }
            else if(stripos($row['title'], "xiaomi")){
                $row['brand']= 6;
            }
            else if(stripos($row['title'], "realme")){
                $row['brand']= 7;
            }
            else if(stripos($row['title'], "samsung")){
                $row['brand']= 3;
            }
            $product[$i]= Product::create([
                'name' => $title,
                // 'seoTitle' => $row['seoTitle'],
                // 'color' => $row['color'],
                'image' => $imageUrl,
                'forwardCameras' => $row['main camera'],
                'backwardCameras' => $row['selfie camera'],
                //'isNew' => $row[6],
                'memoryStorage' =>$row['rom'],
                //'VAT' => $row[8],
                //'status' => $row[9],
                'screen' =>$row['screen'],
                //'isTrending' =>$row[11],
                //'detail' => $row[12],
                //'starRated' =>$row[13],
                //'viewCount' => $row[14],
                'brand_id' => $row['brand'],
                //'metaKeywords' => $row[16],
                //'metaDescriptions' => $row[17],
                'type' => $row['type'],
                'CPU' => $row['cpu'],
                'RAM' => $row['ram'],
                'sim' => $row['sim'],
                'battery' => $row['pin']
            ]);
            $products[] = $product[$i];
            $i++;
        }
        return response()->json([
            'message' => 'Products have been inserted successfully',
            'data' => [
                'products' => $products,
            ],
        ], 201);
    }
}
