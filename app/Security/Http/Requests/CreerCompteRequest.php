<?php

declare(strict_types=1);

namespace App\Security\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreerCompteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prenom'   => ['required', 'string', 'max:255'],
            'nom'      => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms'    => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'prenom.required'    => 'Le prénom est obligatoire.',
            'prenom.max'         => 'Le prénom ne peut pas dépasser 255 caractères.',
            'nom.required'       => 'Le nom est obligatoire.',
            'nom.max'            => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required'     => 'L\'adresse e-mail est obligatoire.',
            'email.email'        => 'L\'adresse e-mail doit être valide.',
            'email.max'          => 'L\'adresse e-mail ne peut pas dépasser 255 caractères.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'terms.required'     => 'Vous devez accepter les conditions d\'utilisation.',
            'terms.accepted'     => 'Vous devez accepter les conditions d\'utilisation.',
        ];
    }
}
