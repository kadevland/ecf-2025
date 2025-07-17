<?php

declare(strict_types=1);

namespace App\Domain\Entities\Film\Components;

use App\Domain\Entities\ComponentEntity\ComponentEntity;

final class FilmComponents implements ComponentEntity
{
    /**
     * @var array<class-string<ComponentEntity>, ComponentEntity>
     */
    private array $components = [];

    private function __construct() {}

    public static function vide(): self
    {
        return new self();
    }

    /**
     * @param  array<ComponentEntity>  $components
     */
    public static function fromComponents(array $components): self
    {
        $aggregate = new self();

        foreach ($components as $component) {
            $aggregate->add($component);
        }

        return $aggregate;
    }

    public function add(ComponentEntity $component): self
    {
        $newAggregate                                = new self();
        $newAggregate->components                    = $this->components;
        $newAggregate->components[$component::class] = $component;

        return $newAggregate;
    }

    /**
     * @param  class-string<ComponentEntity>  $componentClass
     */
    public function remove(string $componentClass): self
    {
        if (! $this->has($componentClass)) {
            return $this;
        }

        $newAggregate             = new self();
        $newAggregate->components = $this->components;
        unset($newAggregate->components[$componentClass]);

        return $newAggregate;
    }

    /**
     * @template T of ComponentEntity
     *
     * @param  class-string<T>  $componentClass
     * @return T|null
     */
    public function get(string $componentClass): ?ComponentEntity
    {
        /** @var T|null */
        return $this->components[$componentClass] ?? null;
    }

    /**
     * @param  class-string<ComponentEntity>  $componentClass
     */
    public function has(string $componentClass): bool
    {
        return isset($this->components[$componentClass]);
    }

    public function getImages(): ?ImagesFilm
    {
        /** @var ImagesFilm|null */
        return $this->get(ImagesFilm::class);
    }

    public function getRevuesPresse(): ?RevuesPresse
    {
        /** @var RevuesPresse|null */
        return $this->get(RevuesPresse::class);
    }

    public function withImages(ImagesFilm $images): self
    {
        return $this->add($images);
    }

    public function withRevuesPresse(RevuesPresse $revues): self
    {
        return $this->add($revues);
    }

    /**
     * @return array<ComponentEntity>
     */
    public function getAllComponents(): array
    {
        return array_values($this->components);
    }

    /**
     * @return array<class-string<ComponentEntity>>
     */
    public function getComponentTypes(): array
    {
        return array_keys($this->components);
    }

    public function isEmpty(): bool
    {
        return empty($this->components);
    }

    public function count(): int
    {
        return count($this->components);
    }
}
