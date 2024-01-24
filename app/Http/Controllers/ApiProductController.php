<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ApiProductController extends Controller
{
public function filter(Request $request) {
        if (auth()->user()->role == 'admin') {
            $products = Product::with('user')->get();
        } elseif (auth()->user()->role == 'seller') {
            $products = auth()->user()->products;
        } else {
            // Handle other roles or return an appropriate response
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Apply filters
        if ($request->filterEmail)
            $products = $products->where('user.email', $request->filterEmail);
        if ($request->filterProductName)
            $products = $products->where('title', $request->filterProductName);
        if ($request->filterDescription)
            $products = $products->where('description', $request->filterDescription);
        if ($request->filterPriceMin && $request->filterPriceMax)
            $products = $products->whereBetween('price', [$request->filterPriceMin, $request->filterPriceMax]);
        if ($request->filterInventoryMin && $request->filterInventoryMax)
            $products = $products->whereBetween('inventory', [$request->filterInventoryMin, $request->filterInventoryMax]);

        $filteredProducts = $products->toJson();

        return response()->json(['products' => json_decode($filteredProducts)]);
}
    public function index() {
        $products = Product::get();
        return response()->json([
            'products' => $products
        ],200);
    }

    public function create() {
        return response()->json([
            'text' => 'This endpoint is not supported for API.'
        ],405);
    }

    public function store(Request $request) {
        $products = Product::create([
            'title' => $request->product_name,
            'price' => $request->price,
            'inventory' => $request->amount_available,
            'description' => $request->explanation,
            'user_id' => $request->user_id,
        ]);
        return response()->json([
            'product' => $products,
            'message'=>true
        ]);
    }

    public function update(Request $request , $id) {
        Product::where('id' , $id)->update([
            'title' => $request->product_name,
            'price' => $request->price,
            'inventory' => $request->amount_available,
            'description' => $request->explanation,
            'updated_at'=>date('Y-m-d H:i:s'),
        ]);
        return response()->json(['text' => 'Product updated successfully.']
        ,201);
    }
    public function edit($id) {
        return response()->json(['message' => 'This endpoint is not supported for API.']
            , 405);

    }

    public function destroy($id) {
        Product::where('id' , $id)->update(['status' => 'disable']);
        return response()->json(['message' => 'Product deleted successfully.']
        , 200);
    }
}
