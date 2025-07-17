<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\Pagination;

use App\Application\DTOs\PaginationInfo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class PaginationLinkRenderer
{
    public function __construct(
        private PaginationInfo $paginationInfo,
        private Request $request,
    ) {}

    /**
     * Retourner le paginator Laravel
     */
    public function paginator(): ?LengthAwarePaginator
    {
        if (! $this->paginationInfo->hasPages()) {
            return null;
        }

        // Créer un LengthAwarePaginator Laravel standard
        $paginator = new LengthAwarePaginator(
            items: [],
            total: $this->paginationInfo->total,
            perPage: $this->paginationInfo->limit,
            currentPage: $this->paginationInfo->currentPage,
            options: [
                'path'     => $this->request->url(),
                'pageName' => 'page',
            ]
        );

        // Ajouter les paramètres de query string
        $paginator->appends($this->request->query());

        return $paginator;
    }

    /**
     * Méthode de compatibilité pour l'ancien système
     */
    public function links(): string
    {
        $paginator = $this->paginator();

        return $paginator ? $paginator->links()
            ->toHtml() : '';
    }
}
