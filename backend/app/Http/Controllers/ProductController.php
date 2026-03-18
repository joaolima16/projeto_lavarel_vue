<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_cost' => 'required|numeric|min:0',
            'price_sale' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value < ($request->price_cost * 1.10)) {
                        $fail('O preço de venda deve ser pelo menos 10% maior que o preço de custo.');
                    }
                },
            ],
        ]);

        $product = Product::create($validated);

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

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price_cost' => 'sometimes|numeric|min:0',
            'price_sale' => [
                'sometimes',
                'numeric',
                function ($attribute, $value, $fail) use ($request, $product) {
                    $custo = $request->input('price_cost', $product->price_cost);
                    if ($value < ($custo * 1.10)) {
                        $fail('O preço de venda deve ser pelo menos 10% maior que o preço de custo.');
                    }
                },
            ],
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}