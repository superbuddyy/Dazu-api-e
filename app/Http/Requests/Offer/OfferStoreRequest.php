<?php

declare(strict_types=1);

namespace App\Http\Requests\Offer;

use App\Enums\CompanyType;
use Illuminate\Foundation\Http\FormRequest;

class OfferStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|max:70',
            'description' => 'nullable|max:17000',
            'price' => 'integer',
            'category' => 'required',
            'lat' => 'required',
            'lon' => 'required',
            'location_name' => 'required',
            'main_image.*' => 'file|max:5000',
            'type' => 'in:' . implode(',', [CompanyType::DEVELOPER, CompanyType::AGENCY, 'user'])
        ];
    }
}
