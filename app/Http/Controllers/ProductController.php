<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|string|max:255',
            'stock' => 'required|integer',
            'sold' => 'required|integer',
            'status' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'expDate' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048', // Validate the file input
            'seniorPWD_discountable' => 'nullable|in:yes,no',
            'on_sale' => 'nullable|in:yes,no',
            'on_sale_price' => 'nullable|numeric',
            'featured' => 'required|in:true,false',
        ]);

        $product = new Product($request->except('image'));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }

        $product->save();

        return response()->json(['message' => 'Product added successfully', 'product' => $product], 201);
    }


    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json(['product' => $product], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|string|max:255',
            'stock' => 'required|integer',
            'sold' => 'required|integer',
            'status' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'expDate' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'seniorPWD_discountable' => 'nullable|in:yes,no',
            'on_sale' => 'required|in:yes,no',
            'on_sale_price' => 'required|numeric',
            'featured' => 'required|in:true,false',
        ]);

        $product = Product::findOrFail($id);

        $product->update($validated);

        if ($request->hasFile('image')) {
            // Handle image upload
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
            $product->save();
        }

        return response()->json(['product' => $product], 200);
    }




    //Dedicated for seniorPWD_discoubtable 
    public function updateDiscountable(Request $request, $id)
{
    // Validate only the seniorPWD_discountable field
    $validated = $request->validate([
        'seniorPWD_discountable' => 'required|in:yes,no',
    ]);

    // Find the product
    $product = Product::findOrFail($id);

    // Update only the seniorPWD_discountable field
    $product->seniorPWD_discountable = $validated['seniorPWD_discountable'];
    $product->save();

    return response()->json(['message' => 'Discountable status updated successfully.'], 200);
}

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to delete product'], 500);
        }
    }

    public function featured_products(Request $request){
       
        $featuredProducts = Product::where('business_id', $request->business_id)
                            ->where('featured', 'true')->get();
        if ($featuredProducts->isEmpty()) {
            return response()->json(['error' => 'No featured products found'], 404);
        }
        $productsArray=[];

        foreach($featuredProducts as $product){
            $productsArray[]=[
                'product_name' => $product->name,
                'product_img' => $product->image
            ];
        }
        return response()->json($productsArray);
    }
}