<?php

declare(strict_types=1);

namespace App\Domain\Entities\Film\Components;

use App\Domain\Entities\ComponentEntity\ComponentEntity;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class RevuesPresse implements ComponentEntity
{
    private const int MAX_REVUES = 50;

    /**
     * @param  array<array{source: string, titre: string, extrait: string, note?: float|null, url?: string|null, date: CarbonImmutable}>  $revues
     */
    private function __construct(
        private array $revues = []
    ) {
        $this->validerRevues();
    }

    public static function vide(): self
    {
        return new self();
    }

    public function ajouterRevue(
        string $source,
        string $titre,
        string $extrait,
        ?float $note = null,
        ?string $url = null,
        ?CarbonImmutable $date = null
    ): self {
        $this->validerNouvelleRevue($source, $titre, $extrait, $note, $url);

        if (count($this->revues) >= self::MAX_REVUES) {
            throw new InvalidArgumentException('Nombre maximum de revues atteint');
        }

        $nouvellesRevues   = $this->revues;
        $nouvellesRevues[] = [
            'source'  => $source,
            'titre'   => $titre,
            'extrait' => $extrait,
            'note'    => $note,
            'url'     => $url,
            'date'    => $date ?? CarbonImmutable::now(),
        ];

        return new self($nouvellesRevues);
    }

    /**
     * @return array<array{source: string, titre: string, extrait: string, note?: float|null, url?: string|null, date: CarbonImmutable}>
     */
    public function getRevues(): array
    {
        return $this->revues;
    }

    /**
     * @return array<array{source: string, titre: string, extrait: string, note: float, url?: string|null, date: CarbonImmutable}>
     */
    public function getRevuesAvecNote(): array
    {
        // @phpstan-ignore notIdentical.alwaysTrue
        return array_filter($this->revues, fn (array $revue) => isset($revue['note']) && $revue['note'] !== null);
    }

    public function getNoteMovenne(): ?float
    {
        $revuesAvecNote = $this->getRevuesAvecNote();

        if (empty($revuesAvecNote)) {
            return null;
        }

        $totalNotes = array_sum(array_column($revuesAvecNote, 'note'));

        return round($totalNotes / count($revuesAvecNote), 1);
    }

    public function nombreRevues(): int
    {
        return count($this->revues);
    }

    /**
     * @return array<array{source: string, titre: string, extrait: string, note?: float|null, url?: string|null, date: CarbonImmutable}>
     */
    public function getRevuesRecentes(int $limite = 5): array
    {
        $revuesTriees = $this->revues;
        usort($revuesTriees, fn (array $a, array $b) => $b['date']->gt($a['date']) ? 1 : -1);

        return array_slice($revuesTriees, 0, $limite);
    }

    private function validerRevues(): void
    {
        if (count($this->revues) > self::MAX_REVUES) {
            throw new InvalidArgumentException('Trop de revues de presse');
        }

        foreach ($this->revues as $revue) {
            $this->validerRevue($revue);
        }
    }

    private function validerNouvelleRevue(
        string $source,
        string $titre,
        string $extrait,
        ?float $note,
        ?string $url
    ): void {
        $this->validerSource($source);
        $this->validerTitre($titre);
        $this->validerExtrait($extrait);

        if ($note !== null) {
            $this->validerNote($note);
        }

        if ($url !== null) {
            $this->validerUrl($url);
        }
    }

    /**
     * @param  array{source: string, titre: string, extrait: string, note?: float|null, url?: string|null, date: CarbonImmutable}  $revue
     */
    private function validerRevue(array $revue): void
    {
        $this->validerSource($revue['source']);
        $this->validerTitre($revue['titre']);
        $this->validerExtrait($revue['extrait']);

        // @phpstan-ignore notIdentical.alwaysTrue
        if (isset($revue['note']) && $revue['note'] !== null) {
            $this->validerNote($revue['note']);
        }

        // @phpstan-ignore notIdentical.alwaysTrue
        if (isset($revue['url']) && $revue['url'] !== null) {
            $this->validerUrl($revue['url']);
        }
    }

    private function validerSource(string $source): void
    {
        try {
            v::stringType()->notEmpty()->length(1, 100)->assert($source);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Source invalide: doit contenir entre 1 et 100 caractères');
        }
    }

    private function validerTitre(string $titre): void
    {
        try {
            v::stringType()->notEmpty()->length(1, 200)->assert($titre);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Titre invalide: doit contenir entre 1 et 200 caractères');
        }
    }

    private function validerExtrait(string $extrait): void
    {
        try {
            v::stringType()->notEmpty()->length(10, 1000)->assert($extrait);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Extrait invalide: doit contenir entre 10 et 1000 caractères');
        }
    }

    private function validerNote(float $note): void
    {
        if ($note < 0 || $note > 5) {
            throw new InvalidArgumentException('Note invalide: doit être entre 0 et 5');
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
