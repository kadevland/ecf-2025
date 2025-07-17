<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Client;

use Illuminate\Foundation\Http\FormRequest;

final class ClientSearchRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recherche'    => ['nullable', 'string', 'max:255'],
            'page'         => ['nullable', 'integer', 'min:1'],
            'perPage'      => ['nullable', 'integer', 'in:15,25,50,100'],
            'sort'         => ['nullable', 'string', 'in:email,statut'],
            'direction'    => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'recherche' => 'terme de recherche',
            'page'      => 'numéro de page',
            'perPage'   => 'nombre d\'éléments par page',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'recherche.max'    => 'Le terme de recherche ne peut pas dépasser :max caractères.',
            'page.integer'     => 'Le numéro de page doit être un nombre entier.',
            'page.min'         => 'Le numéro de page doit être supérieur ou égal à :min.',
            'perPage.integer'  => 'Le nombre d\'éléments par page doit être un nombre entier.',
            'perPage.in'       => 'Le nombre d\'éléments par page doit être 15, 25, 50 ou 100.',
        ];
    }

    /**
     * Prépare les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // Appliquer les valeurs par défaut pour la pagination et caster en int
        if (! $this->has('page') || $this->input('page') === null || $this->input('page') === '') {
            $this->merge(['page' => 1]);
        } else {
            $page = $this->input('page');
            if (is_numeric($page)) {
                $this->merge(['page' => (int) $page]);
            }
        }

        if (! $this->has('perPage') || $this->input('perPage') === null || $this->input('perPage') === '') {
            $this->merge(['perPage' => 15]);
        } else {
            $perPage = $this->input('perPage');
            if (is_numeric($perPage)) {
                $this->merge(['perPage' => (int) $perPage]);
            }
        }
    }
}
