<?php

declare(strict_types=1);

namespace App\Http\Requests\Offer;

use App\Enums\OfferType;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class ReportOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'required',
            'recaptcha' => ['required', new Recaptcha()],
        ];
    }
}
