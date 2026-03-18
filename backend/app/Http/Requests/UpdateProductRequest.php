<?php

namespace App\Http\Requests;


use App\Models\Product;

class UpdateProductRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price_cost' => 'sometimes|numeric|min:0',
            'price_sale' => [
                'sometimes',
                'numeric',
                function ($attribute, $value, $fail) {
                    $priceCost = $this->input('price_cost');

                    if ($priceCost === null) {
                        $product = Product::find($this->route('id'));
                        if ($product) {
                            $priceCost = $product->price_cost;
                        }
                    }

                    if (is_numeric($priceCost) && $value < ($priceCost * 1.10)) {
                        $fail('O preço de venda deve ser pelo menos 10% maior que o preço de custo.');
                    }
                },
            ],
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,png',
        ];
    }
}
