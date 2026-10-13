<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PredictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_type' => ['required', 'string', 'in:Apartment,Villa,Independent House,Builder Floor,Penthouse,Plot,Studio'],
            'transaction_type' => ['nullable', 'string', 'in:Sale,Rent'],
            'city' => ['required', 'string', 'max:100'],
            'locality' => ['required', 'string', 'max:100'],
            'area' => ['required', 'numeric', 'min:100', 'max:25000'],
            'carpet_area' => ['nullable', 'numeric', 'min:50', 'max:25000'],
            'bedrooms' => ['required', 'integer', 'min:1', 'max:10'],
            'bathrooms' => ['required', 'integer', 'min:1', 'max:10'],
            'balconies' => ['nullable', 'integer', 'min:0', 'max:10'],
            'floor' => ['nullable', 'integer', 'min:0', 'max:100'],
            'total_floors' => ['nullable', 'integer', 'min:1', 'max:100'],
            'property_age' => ['nullable', 'integer', 'min:0', 'max:100'],
            'furnishing' => ['required', 'string', 'in:Unfurnished,Semi-Furnished,Fully Furnished'],
            'parking' => ['nullable', 'boolean'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string'],
            'user_asking_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
