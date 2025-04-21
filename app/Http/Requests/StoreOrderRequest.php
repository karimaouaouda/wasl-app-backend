<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'source_app' => ['required', 'string'],
            'restaurant_data' => ['required', 'array'],
            'restaurant_data.name' => ['required', 'string'],
            'restaurant_data.logo_url' => ['string'],
            'restaurant_data.description' => ['string'],
            'items' => ['required', 'array'],
            'items.*.item_name' => ['required', 'string'],
            'items.*.extra_description' => ['string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:1'],
        ];
    }
}
