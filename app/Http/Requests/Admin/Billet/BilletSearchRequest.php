<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Billet;

use App\Domain\Enums\TypeTarif;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request pour la recherche de billets
 */
final class BilletSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recherche'           => ['nullable', 'string', 'max:255'],
            'reservationId'       => ['nullable', 'string', 'max:255'],
            'seanceId'            => ['nullable', 'string', 'max:255'],
            'typeTarif'           => ['nullable', 'string', Rule::in(array_column(TypeTarif::cases(), 'value'))],
            'utilise'             => ['nullable', 'string', 'in:0,1,true,false'],
            'dateUtilisationFrom' => ['nullable', 'date'],
            'dateUtilisationTo'   => ['nullable', 'date', 'after_or_equal:dateUtilisationFrom'],
            'page'                => ['nullable', 'integer', 'min:1'],
            'perPage'             => ['nullable', 'integer', 'in:15,25,50,100'],
            'sortBy'              => ['nullable', 'string', 'in:numero_billet,place,type_tarif,prix,utilise,date_utilisation,created_at'],
            'sortDirection'       => ['nullable', 'string', 'in:asc,desc'],
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

        // Nettoyage des valeurs vides pour les champs optionnels
        if ($this->input('reservationId') === '') {
            $this->merge(['reservationId' => null]);
        }

        if ($this->input('seanceId') === '') {
            $this->merge(['seanceId' => null]);
        }

        if ($this->input('typeTarif') === '') {
            $this->merge(['typeTarif' => null]);
        }

        if ($this->input('utilise') === '') {
            $this->merge(['utilise' => null]);
        }
    }
}
