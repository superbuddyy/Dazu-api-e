<?php

declare(strict_types=1);

namespace App\Http\Requests\Contact;

use App\Enums\OfferType;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required',
            'name' => 'required',
            'message' => 'required',
            'recaptcha' => ['required', new Recaptcha()],
        ];
    }
}
