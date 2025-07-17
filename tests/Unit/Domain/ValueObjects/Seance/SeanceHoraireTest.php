<?php

declare(strict_types=1);

use App\Domain\ValueObjects\Seance\CreneauHoraire;
use App\Domain\ValueObjects\Seance\SeanceHoraire;
use Carbon\CarbonImmutable;

describe('SeanceHoraire Value Object', function () {

    describe('Construction et Factory Methods', function () {
        it('crée un SeanceHoraire avec un CreneauHoraire et temps de préparation', function () {
            $debut       = CarbonImmutable::create(2025, 1, 15, 20, 0);
            $creneauFilm = CreneauHoraire::fromDebutEtDuree($debut, 120);

            $seanceHoraire = SeanceHoraire::create($creneauFilm, 20);

            expect($seanceHoraire->debut())->toEqual($debut);
            expect($seanceHoraire->fin())->toEqual($debut->addMinutes(140)); // 120 + 20
            expect($seanceHoraire->dureeFilmMinutes())->toBe(120);
            expect($seanceHoraire->dureeTotaleMinutes())->toBe(140);
        });

        it('crée un SeanceHoraire depuis un début et une durée', function () {
            $debut = CarbonImmutable::create(2025, 1, 15, 14, 30);

            $seanceHoraire = SeanceHoraire::fromDebutEtDuree($debut, 95, 15);

            expect($seanceHoraire->debut())->toEqual($debut);
            expect($seanceHoraire->fin())->toEqual($debut->addMinutes(110)); // 95 + 15
            expect($seanceHoraire->dureeFilmMinutes())->toBe(95);
            expect($seanceHoraire->dureeTotaleMinutes())->toBe(110);
        });

        it('utilise 20 minutes par défaut pour le temps de préparation', function () {
            $debut = CarbonImmutable::create(2025, 1, 15, 18, 0);

            $seanceHoraire = SeanceHoraire::fromDebutEtDuree($debut, 100);

            expect($seanceHoraire->dureeTotaleMinutes())->toBe(120); // 100 + 20
        });
    });

    describe('Modifications d\'horaires', function () {
        beforeEach(function () {
            $this->debut         = CarbonImmutable::create(2025, 1, 15, 20, 0);
            $this->seanceHoraire = SeanceHoraire::fromDebutEtDuree($this->debut, 120, 20);
        });

        it('change la date en conservant l\'heure', function () {
            $nouvelleDate = CarbonImmutable::create(2025, 2, 20);

            $nouveau = $this->seanceHoraire->changerDate($nouvelleDate);

            expect($nouveau->debut())->toEqual(CarbonImmutable::create(2025, 2, 20, 20, 0));
            expect($nouveau->dureeFilmMinutes())->toBe(120);
            expect($nouveau->dureeTotaleMinutes())->toBe(140);
        });

        it('change l\'heure de début', function () {
            $nouveau = $this->seanceHoraire->changerHeure(15, 45);

            expect($nouveau->debut())->toEqual(CarbonImmutable::create(2025, 1, 15, 15, 45));
            expect($nouveau->dureeFilmMinutes())->toBe(120);
        });

        it('change le temps de préparation', function () {
            $nouveau = $this->seanceHoraire->changerTempsPreparation(30);

            expect($nouveau->debut())->toEqual($this->debut);
            expect($nouveau->fin())->toEqual($this->debut->addMinutes(150)); // 120 + 30
            expect($nouveau->dureeTotaleMinutes())->toBe(150);
        });

        it('décale la séance de plusieurs minutes', function () {
            $nouveau = $this->seanceHoraire->decaler(45);

            expect($nouveau->debut())->toEqual($this->debut->addMinutes(45));
            expect($nouveau->fin())->toEqual($this->debut->addMinutes(185)); // 45 + 120 + 20
        });

        it('décale la séance vers le passé avec un nombre négatif', function () {
            $nouveau = $this->seanceHoraire->decaler(-30);

            expect($nouveau->debut())->toEqual($this->debut->subMinutes(30));
            expect($nouveau->fin())->toEqual($this->debut->addMinutes(110)); // -30 + 120 + 20
        });
    });

    describe('Détection de conflits', function () {
        beforeEach(function () {
            $this->seance1 = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2025, 1, 15, 20, 0),
                120, // Film de 2h
                20   // 20min de préparation
            );
        });

        it('détecte un conflit quand les séances se chevauchent', function () {
            // Séance qui commence avant la fin de la première
            $seance2 = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2025, 1, 15, 21, 30), // Début pendant le film
                90,
                15
            );

            expect($this->seance1->estEnConflit($seance2))->toBeTrue();
            expect($seance2->estEnConflit($this->seance1))->toBeTrue(); // Symétrie
        });

        it('détecte un conflit quand une séance commence pendant la préparation', function () {
            // Séance qui commence pendant le temps de préparation
            $seance2 = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2025, 1, 15, 22, 10), // Pendant la préparation
                60,
                15
            );

            expect($this->seance1->estEnConflit($seance2))->toBeTrue();
        });

        it('ne détecte pas de conflit quand les séances sont consécutives', function () {
            // Séance qui commence exactement à la fin de la première (avec préparation)
            $seance2 = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2025, 1, 15, 22, 20), // Fin exacte de la première
                90,
                15
            );

            expect($this->seance1->estEnConflit($seance2))->toBeFalse();
            expect($seance2->estEnConflit($this->seance1))->toBeFalse(); // Symétrie
        });

        it('ne détecte pas de conflit pour des séances distantes', function () {
            $seance2 = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2025, 1, 16, 10, 0), // Jour suivant
                120,
                20
            );

            expect($this->seance1->estEnConflit($seance2))->toBeFalse();
        });
    });

    describe('Comparaisons et égalité', function () {
        it('considère deux SeanceHoraire identiques comme égaux', function () {
            $debut   = CarbonImmutable::create(2025, 1, 15, 20, 0);
            $seance1 = SeanceHoraire::fromDebutEtDuree($debut, 120, 20);
            $seance2 = SeanceHoraire::fromDebutEtDuree($debut, 120, 20);

            expect($seance1->equals($seance2))->toBeTrue();
            expect($seance2->equals($seance1))->toBeTrue(); // Symétrie
        });

        it('considère deux SeanceHoraire différents comme inégaux', function () {
            $debut   = CarbonImmutable::create(2025, 1, 15, 20, 0);
            $seance1 = SeanceHoraire::fromDebutEtDuree($debut, 120, 20);
            $seance2 = SeanceHoraire::fromDebutEtDuree($debut, 120, 15); // Temps de préparation différent

            expect($seance1->equals($seance2))->toBeFalse();
        });
    });

    describe('Validation et Règles Métier', function () {
        it('rejette une durée de film négative ou nulle', function () {
            $debut = CarbonImmutable::create(2025, 1, 15, 20, 0);

            expect(fn () => SeanceHoraire::fromDebutEtDuree($debut, 0))
                ->toThrow(InvalidArgumentException::class, 'La durée du film doit être positive');

            expect(fn () => SeanceHoraire::fromDebutEtDuree($debut, -30))
                ->toThrow(InvalidArgumentException::class, 'La durée du film doit être positive');
        });

        it('rejette un temps de préparation négatif', function () {
            $debut       = CarbonImmutable::create(2025, 1, 15, 20, 0);
            $creneauFilm = CreneauHoraire::fromDebutEtDuree($debut, 120);

            expect(fn () => SeanceHoraire::create($creneauFilm, -5))
                ->toThrow(InvalidArgumentException::class, 'Le temps inter-séance ne peut pas être négatif');
        });

        it('rejette un temps de préparation trop long', function () {
            $debut       = CarbonImmutable::create(2025, 1, 15, 20, 0);
            $creneauFilm = CreneauHoraire::fromDebutEtDuree($debut, 120);

            expect(fn () => SeanceHoraire::create($creneauFilm, 150))
                ->toThrow(InvalidArgumentException::class, 'Le temps inter-séance ne peut pas dépasser 2 heures');
        });

        it('rejette des heures invalides lors du changement d\'heure', function () {
            $seanceHoraire = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2025, 1, 15, 20, 0),
                120
            );

            expect(fn () => $seanceHoraire->changerHeure(25, 0))
                ->toThrow(InvalidArgumentException::class, 'L\'heure doit être entre 0 et 23');

            expect(fn () => $seanceHoraire->changerHeure(20, 65))
                ->toThrow(InvalidArgumentException::class, 'Les minutes doivent être entre 0 et 59');

            expect(fn () => $seanceHoraire->changerHeure(-1, 30))
                ->toThrow(InvalidArgumentException::class, 'L\'heure doit être entre 0 et 23');
        });
    });

    describe('Immutabilité et Structure', function () {
        it('est immutable par design', function () {
            $classReflection = new ReflectionClass(SeanceHoraire::class);
            expect($classReflection->isReadOnly())->toBeTrue();
            expect($classReflection->isFinal())->toBeTrue();

            $properties = $classReflection->getProperties();
            foreach ($properties as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });

        it('retourne une nouvelle instance lors des modifications', function () {
            $original = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2025, 1, 15, 20, 0),
                120,
                20
            );

            $nouveau = $original->changerHeure(21, 0);

            expect($nouveau)->not->toBe($original);
            expect($original->debut()->hour)->toBe(20); // L'original n'a pas changé
            expect($nouveau->debut()->hour)->toBe(21);
        });
    });

    describe('Cas limites et edge cases', function () {
        it('gère correctement les changements de date avec les années bissextiles', function () {
            $seanceHoraire = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2024, 2, 28, 20, 0), // 2024 est bissextile
                120
            );

            $nouveau = $seanceHoraire->changerDate(CarbonImmutable::create(2024, 2, 29));

            expect($nouveau->debut()->day)->toBe(29);
            expect($nouveau->debut()->month)->toBe(2);
            expect($nouveau->debut()->year)->toBe(2024);
        });

        it('gère les changements d\'heure à minuit', function () {
            $seanceHoraire = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2025, 1, 15, 12, 30),
                120
            );

            $nouveau = $seanceHoraire->changerHeure(0, 0);

            expect($nouveau->debut()->hour)->toBe(0);
            expect($nouveau->debut()->minute)->toBe(0);
        });

        it('gère les décalages qui changent de jour', function () {
            $seanceHoraire = SeanceHoraire::fromDebutEtDuree(
                CarbonImmutable::create(2025, 1, 15, 23, 30),
                120
            );

            $nouveau = $seanceHoraire->decaler(60); // +1h = passage au jour suivant

            expect($nouveau->debut()->day)->toBe(16);
            expect($nouveau->debut()->hour)->toBe(0);
            expect($nouveau->debut()->minute)->toBe(30);
        });
    });
});
