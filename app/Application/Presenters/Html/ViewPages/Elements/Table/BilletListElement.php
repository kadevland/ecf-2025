<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\DTOs\PaginationInfo;
use App\Application\UseCases\Billet\AfficherBillets\AfficherBilletsResponse;
use App\Http\Requests\Admin\Billet\BilletSearchRequest;
use Illuminate\Support\Collection;

/**
 * Element de vue pour la liste des billets
 */
final readonly class BilletListElement
{
    public function __construct(
        public Collection $billets,
        public ?PaginationInfo $pagination = null,
        public string $resetUrl = '',
        public string $currentUrl = '',
    ) {}

    /**
     * Créer un BilletListElement depuis une réponse du UseCase
     */
    public static function creer(AfficherBilletsResponse $response, BilletSearchRequest $request): self
    {
        $billets = collect();

        foreach ($response->billets as $billet) {
            $billets->push(BilletItemListElement::creer($billet));
        }

        return new self(
            billets: $billets,
            pagination: $response->pagination,
            resetUrl: route('gestion.supervision.billets.index'),
            currentUrl: $request->fullUrl(),
        );
    }

    public function isEmpty(): bool
    {
        return $this->billets->isEmpty();
    }

    public function count(): int
    {
        return $this->billets->count();
    }

    /**
     * Obtenir l'URL de réinitialisation des filtres
     */
    public function resetUrl(): string
    {
        return $this->resetUrl;
    }
}
