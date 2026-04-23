<?php

declare(strict_types=1);

namespace App\Auth\Http\SignIn;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SignInRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string|Password>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', Password::default()],
        ];
    }

    public function authorize(): true
    {
        return true;
    }
}
