<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Incident;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request pour la recherche d'incidents
 */
final class IncidentSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recherche' => ['nullable', 'string', 'max:255'],
            'page'      => ['nullable', 'integer', 'min:1'],
            'perPage'   => ['nullable', 'integer', 'in:15,25,50,100'],
            'sort'      => ['nullable', 'string', 'in:titre,created_at'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

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
