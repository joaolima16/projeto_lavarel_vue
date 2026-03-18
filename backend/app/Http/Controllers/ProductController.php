<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_cost' => 'required|numeric|min:0',
            'price_sale' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $priceCost = $request->input('price_cost');
                    if (is_numeric($priceCost) && $value < ($priceCost * 1.10)) {
                        $fail('O preço de venda deve ser pelo menos 10% maior que o preço de custo.');
                    }
                },
            ],
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,png',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        $productData = collect($data)->except('images')->toArray();
        
        $product = Product::create($productData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                ]);
            }
        }
        return response()->json($product, 201);
    }

    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price_cost' => 'sometimes|numeric|min:0',
            'price_sale' => [
                'sometimes',
                'numeric',
                function ($attribute, $value, $fail) use ($request, $product) {
                    $cost = $request->input('price_cost') ?? $product->price_cost;
                    if (is_numeric($cost) && $value < ($cost * 1.10)) {
                        $fail('O preço de venda deve ser pelo menos 10% maior que o preço de custo.');
                    }
                },
            ],
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,png',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        $productData = collect($data)->except('images')->toArray();

        $product->update($productData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                ]);
            }
        }

        return response()->json(['message' => 'Product updated', 'product' => $product]);
    }

    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted'], 204);
    }
}