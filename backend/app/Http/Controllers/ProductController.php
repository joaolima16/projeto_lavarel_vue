<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Assumindo que existe a relação 'images' no model Product
        return ProductResource::collection(Product::with('images')->latest()->get());
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $productData = collect($data)->except('images')->toArray();
        
        $product = Product::create($productData);

        $this->handleImages($request, $product);

        return response()->json(new ProductResource($product->load('images')), 201);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('images'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $productData = collect($data)->except('images')->toArray();

        $product->update($productData);

        $this->handleImages($request, $product);

        return response()->json([
            'message' => 'Product updated', 
            'product' => new ProductResource($product->load('images'))
        ]);
    }

    public function destroy(Product $product)
    {
        // TODO: Adicionar lógica para deletar as imagens do storage
        $product->delete();

        return response()->json(null, 204);
    }


    private function handleImages(Request $request, Product $product): void
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                ]);
            }
        }
    }
}