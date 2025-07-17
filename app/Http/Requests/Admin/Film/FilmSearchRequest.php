<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Film;

use Illuminate\Foundation\Http\FormRequest;

final class FilmSearchRequest extends FormRequest
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
            'recherche' => ['nullable', 'string', 'max:255'],
            'categorie' => ['nullable', 'string', 'in:action,aventure,comedie,drame,horreur,science_fiction,thriller,romance,animation,documentaire,fantastique,historique,musical,western,policier'],
            'page'      => ['nullable', 'integer', 'min:1'],
            'perPage'   => ['nullable', 'integer', 'in:15,25,50,100'],
            'sort'      => ['nullable', 'string', 'in:titre,categorie,realisateur,duree_minutes,date_sortie,note_moyenne,created_at'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
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
            'categorie' => 'catégorie',
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
            'recherche.max'    => 'Le terme de recherche ne peut pas dépasser :max caractères.',
            'categorie.in'     => 'La catégorie sélectionnée est invalide.',
            'page.integer'     => 'Le numéro de page doit être un nombre entier.',
            'page.min'         => 'Le numéro de page doit être supérieur ou égal à :min.',
            'perPage.integer'  => 'Le nombre d\'éléments par page doit être un nombre entier.',
            'perPage.in'       => 'Le nombre d\'éléments par page doit être 15, 25, 50 ou 100.',
            'sort.in'          => 'Le champ de tri sélectionné est invalide.',
            'direction.in'     => 'La direction de tri doit être "asc" ou "desc".',
        ];
    }

    /**
     * Prépare les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // Normaliser le champ categorie (vide → null)
        if ($this->has('categorie') && $this->input('categorie') === '') {
            $this->merge(['categorie' => null]);
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
