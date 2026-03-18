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
            'description' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value, '<p><br><b><strong>')) {
                        $fail('A descrição contém tags HTML não permitidas. Use apenas: <p>, <br>, <b>, <strong>.');
                    }
                },
            ],
            'price_cost' => 'required|numeric|min:0',
            'price_sale' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $priceCost = $this->input('price_cost');
                    if (is_numeric($priceCost) && $value <= ($priceCost * 1.10)) {
                        $fail('O preço de venda deve ser maior que o preço de custo + 10%.');
                    }
                },
            ],
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,png',
        ];
    }
}
