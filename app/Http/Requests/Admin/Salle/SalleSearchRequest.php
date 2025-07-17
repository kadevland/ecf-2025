<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Salle;

use Illuminate\Foundation\Http\FormRequest;

final class SalleSearchRequest extends FormRequest
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
            'recherche'  => ['nullable', 'string', 'max:255'],
            'etat'       => ['nullable', 'string', 'in:active,maintenance,hors_service,en_renovation,fermee'],
            'page'       => ['nullable', 'integer', 'min:1'],
            'perPage'    => ['nullable', 'integer', 'in:15,25,50,100'],
            'sort'       => ['nullable', 'string', 'in:numero,nom,capacite,etat,qualite_projection,cinema'],
            'direction'  => ['nullable', 'string', 'in:asc,desc'],
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
            'cinema_id' => 'cinéma',
            'etat'      => 'état de la salle',
            'page'      => 'numéro de page',
            'perPage'   => 'nombre d\'éléments par page',
            'sort'      => 'champ de tri',
            'direction' => 'direction du tri',
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
            'recherche.max'     => 'Le terme de recherche ne peut pas dépasser :max caractères.',
            'cinema_id.uuid'    => 'L\'identifiant du cinéma doit être un UUID valide.',
            'etat.in'           => 'L\'état sélectionné est invalide.',
            'page.integer'      => 'Le numéro de page doit être un nombre entier.',
            'page.min'          => 'Le numéro de page doit être supérieur ou égal à :min.',
            'perPage.integer'   => 'Le nombre d\'éléments par page doit être un nombre entier.',
            'perPage.in'        => 'Le nombre d\'éléments par page doit être 15, 25, 50 ou 100.',
            'sort.in'           => 'Le champ de tri sélectionné est invalide.',
            'direction.in'      => 'La direction de tri doit être "asc" ou "desc".',
        ];
    }

    /**
     * Prépare les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // Normaliser le champ etat (vide → null)
        if ($this->has('etat') && $this->input('etat') === '') {
            $this->merge(['etat' => null]);
        }

        // Normaliser le champ cinema_id (vide → null)
        if ($this->has('cinema_id') && $this->input('cinema_id') === '') {
            $this->merge(['cinema_id' => null]);
        }

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
