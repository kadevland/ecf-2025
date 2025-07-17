<?php

declare(strict_types=1);

use App\Domain\ValueObjects\Commun\Email;

describe('Email Value Object', function () {

    describe('Construction et Factory Methods', function () {
        it('crée une adresse e-mail valide avec succès', function () {
            $email = new Email('test@cinephoria.fr');

            expect($email->value)->toBe('test@cinephoria.fr');
        });

        it('accepte divers formats d\'adresses e-mail valides', function (string $validEmail) {
            $email = new Email($validEmail);

            expect($email->value)->toBe($validEmail);
        })->with([
            'user@domain.com',
            'user.name@domain.co.uk',
            'user+tag@domain.org',
            'user123@domain-name.fr',
            'a@b.co',  // Minimum valide
        ]);

        it('accepte une adresse e-mail de longueur maximale', function () {
            // 320 caractères exactement
            $maxEmail = str_repeat('a', 309).'@domain.com';
            $email    = new Email($maxEmail);

            expect(mb_strlen($email->value))->toBe(320);
        });
    });

    describe('Getters et Accesseurs', function () {
        it('expose la valeur de l\'email', function () {
            $email = new Email('test@cinephoria.fr');

            expect($email->value)->toBe('test@cinephoria.fr');
        });

        it('extrait correctement le domaine', function (string $emailAddress, string $expectedDomain) {
            $email = new Email($emailAddress);

            expect($email->domain())->toBe($expectedDomain);
        })->with([
            ['client@cinephoria.fr', 'cinephoria.fr'],
            ['employee@cinephoria.be', 'cinephoria.be'],
            ['admin@staff.cinephoria.fr', 'staff.cinephoria.fr'],
            ['user@domain.com', 'domain.com'],
            ['user.name@domain.co.uk', 'domain.co.uk'],
            ['user+tag@domain.org', 'domain.org'],
            ['user123@domain-name.fr', 'domain-name.fr'],
            ['a@b.co', 'b.co'],
        ]);
    });

    describe('Comparaisons', function () {
        it('considère deux emails identiques comme égaux', function () {
            $email1 = new Email('test@cinephoria.fr');
            $email2 = new Email('test@cinephoria.fr');

            expect($email1->equals($email2))->toBeTrue();
            expect($email2->equals($email1))->toBeTrue(); // Symétrie
        });

        it('considère deux emails différents comme inégaux', function () {
            $email1 = new Email('test@cinephoria.fr');
            $email2 = new Email('autre@cinephoria.fr');

            expect($email1->equals($email2))->toBeFalse();
            expect($email2->equals($email1))->toBeFalse(); // Symétrie
        });

        it('est sensible à la casse pour l\'égalité', function () {
            $email1 = new Email('Test@Cinephoria.FR');
            $email2 = new Email('test@cinephoria.fr');

            expect($email1->equals($email2))->toBeFalse();
        });
    });

    describe('Validation et Règles Métier', function () {
        it('rejette les formats d\'adresses e-mail invalides', function (string $invalidEmail) {
            expect(fn () => new Email($invalidEmail))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            'invalid-email',
            '@domain.com',
            'user@',
            'user@domain',
            'user space@domain.com',
            '',
            'user@@domain.com',
        ]);

        it('rejette les adresses e-mail trop longues', function () {
            $longEmail = str_repeat('a', 310).'@domain.com'; // > 320 chars

            expect(fn () => new Email($longEmail))
                ->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Immutabilité et Structure', function () {
        it('est immutable par design', function () {
            $classReflection = new ReflectionClass(Email::class);
            expect($classReflection->isReadOnly())->toBeTrue();
            expect($classReflection->isFinal())->toBeTrue();

            $properties = $classReflection->getProperties();
            foreach ($properties as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });
    });
});
