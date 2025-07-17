<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

final class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'     => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'sujet'   => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'cinema'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'     => 'Le nom est obligatoire.',
            'nom.max'          => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required'   => 'L\'adresse email est obligatoire.',
            'email.email'      => 'L\'adresse email doit être valide.',
            'email.max'        => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'sujet.required'   => 'Le sujet est obligatoire.',
            'sujet.max'        => 'Le sujet ne peut pas dépasser 255 caractères.',
            'message.required' => 'Le message est obligatoire.',
            'message.max'      => 'Le message ne peut pas dépasser 2000 caractères.',
            'cinema.max'       => 'Le nom du cinéma ne peut pas dépasser 255 caractères.',
        ];
    }
}
