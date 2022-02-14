<?php

declare(strict_types=1);

namespace App\Http\Requests\Search;

use App\Enums\OfferType;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'nullable|string',
            'type' => 'nullable|string',
            'location[lat]' => 'required_with:location[lon]',
            'location[lon]' => 'required_with:location[lat]',
            'price' => 'nullable|array',
            'price.max' => 'nullable|int',
            'price.min' => 'nullable|int',
            'attributes' => 'nullable|array',
            'attributes.*' => 'required_with:attributes',
        ];
    }
}
