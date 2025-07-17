<?php

declare(strict_types=1);

namespace App\Domain\Entities\Media;

use App\Domain\Events\Media\ImageCreatedEvent;
use App\Domain\Traits\RecordsDomainEvents;
use App\Domain\ValueObjects\Media\ImageId;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class Image
{
    use RecordsDomainEvents;

    public function __construct(
        public private(set) ImageId $id,
        public private(set) string $pathStockage,
        public private(set) string $urlAccesPublic,
        public private(set) bool $isLocal,
        public private(set) string $mimeType,
        public private(set) string $extension,
        public private(set) ? int $dbId,
        public private(set) ? string $altText,
        public private(set) ? int $width,
        public private(set) ? int $height,
        public private(set) ? int $fileSize,
        public private(set) ? float $fileSizeKb,
        public private(set) ? float $fileSizeMb,
        public private(set) CarbonImmutable $createdAt,
        public private(set) CarbonImmutable $updatedAt,
    ) {
        $this->enforceInvariants();
        $this->recordEvent(new ImageCreatedEvent($this->id, $this->urlAccesPublic, $this->isLocal));
    }

    public static function local(
        string $pathStockage,
        string $urlAccesPublic,
        string $mimeType,
        ?int $dbId = null,
        ?string $altText = null,
        ?int $width = null,
        ?int $height = null,
        ?int $fileSize = null
    ): self {
        $now = CarbonImmutable::now();

        return new self(
            ImageId::generate(),
            $pathStockage,
            $urlAccesPublic,
            true,
            $mimeType,
            self::getExtensionFromMimeType($mimeType),
            $dbId,
            $altText,
            $width,
            $height,
            $fileSize,
            $fileSize ? round($fileSize / 1024, 2) : null,
            $fileSize ? round($fileSize / (1024 * 1024), 2) : null,
            $now,
            $now
        );
    }

    public static function external(
        string $urlAccesPublic,
        string $mimeType = 'image/jpeg',
        ?string $altText = null,
        ?int $width = null,
        ?int $height = null
    ): self {
        $now = CarbonImmutable::now();

        return new self(
            ImageId::generate(),
            '',
            $urlAccesPublic,
            false,
            $mimeType,
            self::getExtensionFromMimeType($mimeType),
            null,
            $altText,
            $width,
            $height,
            null,
            null,
            null,
            $now,
            $now
        );
    }

    public function changerAltText(?string $nouvelAltText): void
    {
        if ($nouvelAltText !== null) {
            $this->validerAltText($nouvelAltText);
        }

        if ($this->altText === $nouvelAltText) {
            return;
        }

        $this->altText = $nouvelAltText;
        $this->touch();
    }

    public function changerDimensions(?int $width, ?int $height): void
    {
        $this->validerDimensions($width, $height);

        if ($this->width === $width && $this->height === $height) {
            return;
        }

        $this->width  = $width;
        $this->height = $height;
        $this->touch();
    }

    public function changerDbId(?int $dbId): void
    {
        if ($this->dbId === $dbId) {
            return;
        }

        $this->dbId = $dbId;
        $this->touch();
    }

    public function changerFileSize(?int $fileSize): void
    {
        if ($fileSize !== null && $fileSize < 0) {
            throw new InvalidArgumentException('Taille de fichier invalide');
        }

        if ($this->fileSize === $fileSize) {
            return;
        }

        $this->fileSize = $fileSize;
        $this->touch();
    }

    public function isExternal(): bool
    {
        return ! $this->isLocal;
    }

    public function isPortrait(): bool
    {
        return $this->height && $this->width && $this->height > $this->width;
    }

    public function isLandscape(): bool
    {
        return $this->height && $this->width && $this->width > $this->height;
    }

    public function isSquare(): bool
    {
        return $this->height && $this->width && $this->height === $this->width;
    }

    public function ratio(): float
    {
        return $this->width && $this->height ? round($this->width / $this->height, 2) : 1.0;
    }

    public function hasDimensions(): bool
    {
        return $this->width !== null && $this->height !== null;
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }

    /**
     * @return array<string>
     */
    private static function getSupportedMimeTypes(): array
    {
        return [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'image/bmp',
            'image/tiff',
        ];
    }

    private static function getExtensionFromMimeType(string $mimeType): string
    {
        $extensions = [
            'image/jpeg'    => 'jpg',
            'image/png'     => 'png',
            'image/gif'     => 'gif',
            'image/webp'    => 'webp',
            'image/svg+xml' => 'svg',
            'image/bmp'     => 'bmp',
            'image/tiff'    => 'tiff',
        ];

        return $extensions[$mimeType] ?? 'unknown';
    }

    private function enforceInvariants(): void
    {
        $this->validerPathStockage($this->pathStockage);
        $this->validerUrlAccesPublic($this->urlAccesPublic);
        $this->validerMimeType($this->mimeType);
        $this->validerDimensions($this->width, $this->height);

        if ($this->altText !== null) {
            $this->validerAltText($this->altText);
        }

        if ($this->fileSize !== null && $this->fileSize < 0) {
            throw new InvalidArgumentException('Taille de fichier invalide');
        }

        if ($this->dbId !== null && $this->dbId <= 0) {
            throw new InvalidArgumentException('ID de base de données invalide');
        }
    }

    private function validerPathStockage(string $pathStockage): void
    {
        if ($this->isLocal && empty($pathStockage)) {
            throw new InvalidArgumentException('Le chemin de stockage est obligatoire pour une image locale');
        }
    }

    private function validerUrlAccesPublic(string $urlAccesPublic): void
    {
        try {
            v::url()->assert($urlAccesPublic);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('URL d\'accès public invalide');
        }
    }

    private function validerMimeType(string $mimeType): void
    {
        try {
            v::in(self::getSupportedMimeTypes())->assert($mimeType);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException("Type MIME non supporté: {$mimeType}");
        }
    }

    private function validerDimensions(?int $width, ?int $height): void
    {
        if ($width !== null) {
            try {
                v::intType()->positive()->assert($width);
            } catch (ValidationException $e) {
                throw new InvalidArgumentException('Largeur invalide: doit être positive');
            }
        }

        if ($height !== null) {
            try {
                v::intType()->positive()->assert($height);
            } catch (ValidationException $e) {
                throw new InvalidArgumentException('Hauteur invalide: doit être positive');
            }
        }
    }

    private function validerAltText(string $altText): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(1, 500)
                ->assert($altText);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Texte alternatif invalide: doit contenir entre 1 et 500 caractères');
        }
    }

    private function touch(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }
}
