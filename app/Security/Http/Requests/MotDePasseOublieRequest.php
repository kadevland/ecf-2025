<?php

declare(strict_types=1);

namespace App\Security\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class MotDePasseOublieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email'    => 'L\'adresse e-mail doit être valide.',
        ];
    }
}
