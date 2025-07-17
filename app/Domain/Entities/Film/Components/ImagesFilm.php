<?php

declare(strict_types=1);

namespace App\Domain\Entities\Film\Components;

use App\Domain\Entities\ComponentEntity\ComponentEntity;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class ImagesFilm implements ComponentEntity
{
    private const int MAX_IMAGES = 20;

    /**
     * @param  array<string>  $affiches
     * @param  array<string>  $captures
     * @param  array<string>  $bandesAnnonces
     */
    private function __construct(
        private array $affiches = [],
        private array $captures = [],
        private array $bandesAnnonces = []
    ) {
        $this->validerImages();
    }

    public static function vide(): self
    {
        return new self();
    }

    /**
     * @param  array<string>  $affiches
     * @param  array<string>  $captures
     * @param  array<string>  $bandesAnnonces
     */
    public static function create(array $affiches = [], array $captures = [], array $bandesAnnonces = []): self
    {
        return new self($affiches, $captures, $bandesAnnonces);
    }

    public function ajouterAffiche(string $urlAffiche): self
    {
        $this->validerUrl($urlAffiche);

        if (in_array($urlAffiche, $this->affiches, true)) {
            return $this;
        }

        if (count($this->affiches) >= self::MAX_IMAGES) {
            throw new InvalidArgumentException('Nombre maximum d\'affiches atteint');
        }

        $nouvellesAffiches   = $this->affiches;
        $nouvellesAffiches[] = $urlAffiche;

        return new self($nouvellesAffiches, $this->captures, $this->bandesAnnonces);
    }

    public function ajouterCapture(string $urlCapture): self
    {
        $this->validerUrl($urlCapture);

        if (in_array($urlCapture, $this->captures, true)) {
            return $this;
        }

        if (count($this->captures) >= self::MAX_IMAGES) {
            throw new InvalidArgumentException('Nombre maximum de captures atteint');
        }

        $nouvellesCaptures   = $this->captures;
        $nouvellesCaptures[] = $urlCapture;

        return new self($this->affiches, $nouvellesCaptures, $this->bandesAnnonces);
    }

    public function ajouterBandeAnnonce(string $urlBandeAnnonce): self
    {
        $this->validerUrl($urlBandeAnnonce);

        if (in_array($urlBandeAnnonce, $this->bandesAnnonces, true)) {
            return $this;
        }

        if (count($this->bandesAnnonces) >= 5) {
            throw new InvalidArgumentException('Nombre maximum de bandes-annonces atteint');
        }

        $nouvellesBandes   = $this->bandesAnnonces;
        $nouvellesBandes[] = $urlBandeAnnonce;

        return new self($this->affiches, $this->captures, $nouvellesBandes);
    }

    /**
     * @return array<string>
     */
    public function getAffiches(): array
    {
        return $this->affiches;
    }

    /**
     * @return array<string>
     */
    public function getCaptures(): array
    {
        return $this->captures;
    }

    /**
     * @return array<string>
     */
    public function getBandesAnnonces(): array
    {
        return $this->bandesAnnonces;
    }

    public function getAffichePrincipale(): ?string
    {
        return $this->affiches[0] ?? null;
    }

    public function getBandeAnnoncePrincipale(): ?string
    {
        return $this->bandesAnnonces[0] ?? null;
    }

    public function nombreTotalImages(): int
    {
        return count($this->affiches) + count($this->captures);
    }

    private function validerImages(): void
    {
        if (count($this->affiches) > self::MAX_IMAGES) {
            throw new InvalidArgumentException('Trop d\'affiches');
        }

        if (count($this->captures) > self::MAX_IMAGES) {
            throw new InvalidArgumentException('Trop de captures');
        }

        foreach ($this->affiches as $affiche) {
            $this->validerUrl($affiche);
        }

        foreach ($this->captures as $capture) {
            $this->validerUrl($capture);
        }

        foreach ($this->bandesAnnonces as $bande) {
            $this->validerUrl($bande);
        }
    }

    private function validerUrl(string $url): void
    {
        try {
            v::url()->assert($url);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('URL invalide: '.$url);
        }
    }
}
