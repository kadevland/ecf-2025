<?php

declare(strict_types=1);

use App\Domain\ValueObjects\Commun\AbstractHybridId;

describe('AbstractHybridId Value Object', function () {

    describe('Structure de la classe', function () {
        it('est une classe abstraite readonly', function () {
            $reflection = new ReflectionClass(AbstractHybridId::class);

            expect($reflection->isAbstract())->toBeTrue();
            expect($reflection->isReadOnly())->toBeTrue();
        });

        it('a des propriétés readonly correctes', function () {
            $reflection = new ReflectionClass(AbstractHybridId::class);
            $properties = $reflection->getProperties();

            expect(count($properties))->toBe(2);
            foreach ($properties as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });

        it('définit les bonnes méthodes abstraites', function () {
            $reflection      = new ReflectionClass(AbstractHybridId::class);
            $abstractMethods = array_filter(
                $reflection->getMethods(),
                fn ($method) => $method->isAbstract()
            );

            $abstractMethodNames = array_map(fn ($method) => $method->getName(), $abstractMethods);

            expect($abstractMethodNames)->toContain('fromDatabase');
            expect($abstractMethodNames)->toContain('generate');
            expect($abstractMethodNames)->toContain('fromUuid');
        });
    });
});
