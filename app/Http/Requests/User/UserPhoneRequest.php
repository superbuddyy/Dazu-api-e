<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Enums\OfferType;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class UserPhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recaptcha' => ['required', new Recaptcha()],
        ];
    }
}
