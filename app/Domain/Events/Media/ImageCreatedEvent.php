<?php

declare(strict_types=1);

namespace App\Domain\Events\Media;

use App\Domain\Events\DomainEvent;
use App\Domain\ValueObjects\Media\ImageId;

final readonly class ImageCreatedEvent extends DomainEvent
{
    public function __construct(
        public ImageId $imageId,
        public string $urlAccesPublic,
        public bool $isLocal
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'media.image_created';
    }
}
