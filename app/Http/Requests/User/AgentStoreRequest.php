<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Enums\OfferType;
use Illuminate\Foundation\Http\FormRequest;

class AgentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:300',
            'email' => 'required|email',
        ];
    }
}
