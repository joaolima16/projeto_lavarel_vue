<?php

namespace App\Http\Requests;

class StoreProductRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_cost' => 'required|numeric|min:0',
            'price_sale' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $priceCost = $this->input('price_cost');
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
