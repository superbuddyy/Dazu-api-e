<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Enums\OfferType;
use Illuminate\Foundation\Http\FormRequest;

class SetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required',
            'password' => 'required',
        ];
    }
}
