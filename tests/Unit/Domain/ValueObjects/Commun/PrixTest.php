<?php

declare(strict_types=1);

use App\Domain\ValueObjects\Commun\Prix;

describe('Prix Value Object', function () {

    describe('Construction et Factory Methods', function () {
        it('crée un prix depuis des euros', function () {
            $prix = Prix::fromEuros(10.50);

            expect($prix->getAmount())->toBe(1050); // 10.50€ = 1050 centimes
            expect($prix->getCurrency()->getCode())->toBe('EUR');
        });

        it('crée un prix depuis des centimes', function () {
            $prix = Prix::fromEuroCentimes(1050);

            expect($prix->getAmount())->toBe(1050);
            expect($prix->getCurrency()->getCode())->toBe('EUR');
        });

        it('crée un prix gratuit', function () {
            $prix = Prix::gratuit();

            expect($prix->getAmount())->toBe(0);
            expect($prix->getCurrency()->getCode())->toBe('EUR');
        });

        it('gère correctement l\'arrondissement', function () {
            $prix1 = Prix::fromEuros(10.555); // Devrait arrondir à 10.56€
            $prix2 = Prix::fromEuros(10.554); // Devrait arrondir à 10.55€

            expect($prix1->getAmount())->toBe(1056);
            expect($prix2->getAmount())->toBe(1055);
        });
    });

    describe('Getters et Accesseurs', function () {
        it('retourne le montant en centimes', function () {
            $prix = Prix::fromEuros(12.50);

            expect($prix->getAmount())->toBe(1250);
        });

        it('retourne la devise', function () {
            $prix = Prix::fromEuros(10.00);

            expect($prix->getCurrency()->getCode())->toBe('EUR');
        });
    });

    describe('Prédicats et États', function () {
        it('détermine si un prix est gratuit', function () {
            $gratuit = Prix::gratuit();
            $payant  = Prix::fromEuros(10.00);

            expect($gratuit->estGratuit())->toBeTrue();
            expect($payant->estGratuit())->toBeFalse();
        });

        it('détermine si un prix est positif ou négatif', function () {
            $positif = Prix::fromEuros(10.00);
            $negatif = Prix::fromEuros(10.00)->soustraire(Prix::fromEuros(20.00));
            $zero    = Prix::gratuit();

            expect($positif->isPositif())->toBeTrue();
            expect($positif->isNegatif())->toBeFalse();

            expect($negatif->isPositif())->toBeFalse();
            expect($negatif->isNegatif())->toBeTrue();

            expect($zero->isPositif())->toBeTrue(); // 0 est considéré comme positif
            expect($zero->isNegatif())->toBeFalse();
        });
    });

    describe('Comparaisons', function () {
        it('compare deux prix', function () {
            $prix10    = Prix::fromEuros(10.00);
            $prix20    = Prix::fromEuros(20.00);
            $prix10bis = Prix::fromEuros(10.00);

            expect($prix10->estInferieur($prix20))->toBeTrue();
            expect($prix20->estSuperieur($prix10))->toBeTrue();
            expect($prix10->estSuperieur($prix20))->toBeFalse();
            expect($prix20->estInferieur($prix10))->toBeFalse();

            expect($prix10->equals($prix10bis))->toBeTrue();
            expect($prix10->equals($prix20))->toBeFalse();
        });

        it('gère correctement l\'égalité', function () {
            $prix1 = Prix::fromEuros(10.50);
            $prix2 = Prix::fromEuros(10.50);
            $prix3 = Prix::fromEuroCentimes(1050);
            $prix4 = Prix::fromEuros(10.51);

            expect($prix1->equals($prix2))->toBeTrue();
            expect($prix1->equals($prix3))->toBeTrue();
            expect($prix1->equals($prix4))->toBeFalse();
        });
    });

    describe('Mutations et Opérations', function () {
        it('additionne deux prix', function () {
            $prix1 = Prix::fromEuros(10.50);
            $prix2 = Prix::fromEuros(5.25);

            $total = $prix1->ajouter($prix2);

            expect($total->getAmount())->toBe(1575); // 15.75€
        });

        it('soustrait deux prix', function () {
            $prix1 = Prix::fromEuros(10.50);
            $prix2 = Prix::fromEuros(5.25);

            $difference = $prix1->soustraire($prix2);

            expect($difference->getAmount())->toBe(525); // 5.25€
        });

        it('multiplie un prix', function () {
            $prix = Prix::fromEuros(10.00);

            $double = $prix->multiplier(2);
            $moitie = $prix->multiplier(0.5);

            expect($double->getAmount())->toBe(2000); // 20€
            expect($moitie->getAmount())->toBe(500);  // 5€
        });

        it('applique une réduction', function () {
            $prix = Prix::fromEuros(100.00);

            $avecReduction10 = $prix->appliquerReduction(10);
            $avecReduction50 = $prix->appliquerReduction(50);

            expect($avecReduction10->getAmount())->toBe(9000); // 90€
            expect($avecReduction50->getAmount())->toBe(5000); // 50€
        });

        it('ajoute la TVA avec prix HT', function (float $prixHT, float $tauxTVA, int $prixTTCAttendu) {
            $prix = Prix::fromEuros($prixHT);

            $prixTTC = $prix->avecTVA($tauxTVA);

            expect($prixTTC->getAmount())->toBe($prixTTCAttendu);
        })->with([
            // [prix_ht_euros, taux_tva, prix_ttc_attendu_centimes]
            [10.67, 0.20, 1280],  // 10.67€ HT + 20% TVA → 12.80€ TTC
            [7.92, 0.20, 950],    // 7.92€ HT + 20% TVA → 9.50€ TTC
            [100.00, 0.20, 12000], // 100€ HT + 20% TVA → 120€ TTC
        ]);

        it('retire la TVA avec prix TTC', function (float $prixTTC, float $tauxTVA, int $prixHTAttendu) {
            $prix = Prix::fromEuros($prixTTC);

            $prixHT = $prix->sansTVA($tauxTVA);

            expect($prixHT->getAmount())->toBe($prixHTAttendu);
        })->with([
            // [prix_ttc_euros, taux_tva, prix_ht_attendu_centimes]
            [12.80, 0.20, 1067],  // 12.80€ TTC - 20% TVA → 10.67€ HT
            [9.50, 0.20, 792],    // 9.50€ TTC - 20% TVA → 7.92€ HT
            [120.00, 0.20, 10000], // 120€ TTC - 20% TVA → 100€ HT
        ]);

        it('calcule le montant de TVA', function () {
            $prixTTC = Prix::fromEuros(120.00); // Prix TTC

            $montantTVA = $prixTTC->montantTVA(0.20);

            expect($montantTVA->getAmount())->toBe(2000); // 20€
        });
    });

    describe('Validation et Règles Métier', function () {
        it('rejette les pourcentages de réduction invalides', function (int $pourcentage) {
            $prix = Prix::fromEuros(100.00);

            expect(fn () => $prix->appliquerReduction($pourcentage))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            -1,
            -10,
            101,
            150,
        ]);

        it('rejette un taux de TVA négatif', function () {
            $prix = Prix::fromEuros(100.00);

            expect(fn () => $prix->avecTVA(-0.20))
                ->toThrow(InvalidArgumentException::class, 'TVA rate cannot be negative');

            expect(fn () => $prix->sansTVA(-0.20))
                ->toThrow(InvalidArgumentException::class, 'TVA rate cannot be negative');
        });
    });

    describe('Immutabilité et Structure', function () {
        it('est immutable par design', function () {
            $classReflection = new ReflectionClass(Prix::class);
            expect($classReflection->isReadOnly())->toBeTrue();
            expect($classReflection->isFinal())->toBeTrue();

            // Vérifier que toutes les propriétés sont bien readonly
            $properties = $classReflection->getProperties();
            foreach ($properties as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });

        it('retourne une nouvelle instance lors des opérations', function () {
            $prix1 = Prix::fromEuros(10.00);
            $prix2 = Prix::fromEuros(5.00);

            $resultat = $prix1->ajouter($prix2);

            expect($prix1)->not->toBe($resultat);
            expect($prix1->getAmount())->toBe(1000); // Prix original inchangé
            expect($resultat->getAmount())->toBe(1500);
        });
    });

});
