<?php

declare(strict_types=1);

namespace App\Security\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ConnexionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'L\'adresse email est requise.',
            'email.email'       => 'L\'adresse email doit être valide.',
            'password.required' => 'Le mot de passe est requis.',
        ];
    }

    /**
     * Get the validated credentials for authentication.
     */
    public function getCredentials(): array
    {
        return $this->only(['email', 'password']);
    }
}
