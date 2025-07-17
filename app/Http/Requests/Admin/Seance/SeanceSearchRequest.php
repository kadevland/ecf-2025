<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Seance;

use App\Domain\Enums\EtatSeance;
use App\Domain\Enums\QualiteProjection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SeanceSearchRequest extends FormRequest
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
            'recherche'          => ['nullable', 'string', 'max:255'],
            'etat'               => ['nullable', 'string', Rule::in(array_column(EtatSeance::cases(), 'value'))],
            'qualite_projection' => ['nullable', 'string', Rule::in(array_column(QualiteProjection::cases(), 'value'))],
            'film_id'            => ['nullable', 'integer', 'exists:films,id'],
            'salle_id'           => ['nullable', 'integer', 'exists:salles,id'],
            'date_debut'         => ['nullable', 'date'],
            'date_fin'           => ['nullable', 'date', 'after_or_equal:date_debut'],
            'page'               => ['nullable', 'integer', 'min:1'],
            'perPage'            => ['nullable', 'integer', 'in:15,25,50,100'],
            'sort'               => ['nullable', 'string', 'in:date,film,salle,etat,qualite,prix'],
            'direction'          => ['nullable', 'string', 'in:asc,desc'],
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
            'recherche'          => 'terme de recherche',
            'etat'               => 'état de la séance',
            'qualite_projection' => 'qualité de projection',
            'film_id'            => 'film',
            'salle_id'           => 'salle',
            'date_debut'         => 'date de début',
            'date_fin'           => 'date de fin',
            'page'               => 'numéro de page',
            'perPage'            => 'nombre d\'éléments par page',
            'sort'               => 'champ de tri',
            'direction'          => 'direction du tri',
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
            'recherche.max'               => 'Le terme de recherche ne peut pas dépasser :max caractères.',
            'etat.in'                     => 'L\'état sélectionné n\'est pas valide.',
            'qualite_projection.in'       => 'La qualité de projection sélectionnée n\'est pas valide.',
            'film_id.exists'              => 'Le film sélectionné n\'existe pas.',
            'salle_id.exists'             => 'La salle sélectionnée n\'existe pas.',
            'date_debut.date'             => 'La date de début doit être une date valide.',
            'date_fin.date'               => 'La date de fin doit être une date valide.',
            'date_fin.after_or_equal'     => 'La date de fin doit être postérieure ou égale à la date de début.',
            'page.integer'                => 'Le numéro de page doit être un nombre entier.',
            'page.min'                    => 'Le numéro de page doit être supérieur ou égal à :min.',
            'perPage.integer'             => 'Le nombre d\'éléments par page doit être un nombre entier.',
            'perPage.in'                  => 'Le nombre d\'éléments par page doit être 15, 25, 50 ou 100.',
            'sort.in'                     => 'Le champ de tri sélectionné est invalide.',
            'direction.in'                => 'La direction de tri doit être "asc" ou "desc".',
        ];
    }

    /**
     * Prépare les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // Normaliser les valeurs vides en null
        if ($this->has('etat') && ($this->input('etat') === '' || $this->input('etat') === 'null')) {
            $this->merge(['etat' => null]);
        }

        if ($this->has('qualite_projection') && ($this->input('qualite_projection') === '' || $this->input('qualite_projection') === 'null')) {
            $this->merge(['qualite_projection' => null]);
        }

        if ($this->has('film_id') && ($this->input('film_id') === '' || $this->input('film_id') === 'null')) {
            $this->merge(['film_id' => null]);
        }

        if ($this->has('salle_id') && ($this->input('salle_id') === '' || $this->input('salle_id') === 'null')) {
            $this->merge(['salle_id' => null]);
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

        // Conversion des IDs en entiers
        if ($this->has('film_id') && is_numeric($this->input('film_id'))) {
            $this->merge(['film_id' => (int) $this->input('film_id')]);
        }

        if ($this->has('salle_id') && is_numeric($this->input('salle_id'))) {
            $this->merge(['salle_id' => (int) $this->input('salle_id')]);
        }
    }
}
