<?php

declare(strict_types=1);

use App\Support\UrlBuilder;
use Spatie\Url\Url;

describe('UrlBuilder', function () {
    describe('fromString', function () {
        it('creates UrlBuilder from string', function () {
            $url = UrlBuilder::fromString('https://example.com/test?foo=bar');

            expect($url->toString())->toBe('https://example.com/test?foo=bar');
        });
    });

    describe('constructor', function () {
        it('creates UrlBuilder with Url object', function () {
            $spatieUrl = Url::fromString('https://example.com/test?foo=bar');
            $url       = new UrlBuilder($spatieUrl);

            expect($url->toString())->toBe('https://example.com/test?foo=bar');
        });
    });

    describe('with', function () {
        it('adds query parameter', function () {
            $url = UrlBuilder::fromString('https://example.com/test')
                ->with('search', 'paris');

            expect($url->toString())->toBe('https://example.com/test?search=paris');
        });

        it('modifies existing parameter', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=lyon')
                ->with('search', 'paris');

            expect($url->toString())->toBe('https://example.com/test?search=paris');
        });

        it('is immutable', function () {
            $original = UrlBuilder::fromString('https://example.com/test');
            $modified = $original->with('search', 'paris');

            expect($original->toString())->toBe('https://example.com/test');
            expect($modified->toString())->toBe('https://example.com/test?search=paris');
        });
    });

    describe('remove', function () {
        it('removes query parameter', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=paris&page=2')
                ->remove('page');

            expect($url->toString())->toBe('https://example.com/test?search=paris');
        });

        it('ignores non-existent parameter', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=paris')
                ->remove('page');

            expect($url->toString())->toBe('https://example.com/test?search=paris');
        });
    });

    describe('only', function () {
        it('keeps only specified parameters', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=paris&page=2&sort=nom&operationnel=1')
                ->only(['search', 'operationnel']);

            expect($url->toString())->toBe('https://example.com/test?operationnel=1&search=paris');
        });

        it('handles empty keys array', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=paris&page=2')
                ->only([]);

            expect($url->toString())->toBe('https://example.com/test');
        });
    });

    describe('except', function () {
        it('removes specified parameters', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=paris&page=2&sort=nom')
                ->except(['page', 'sort']);

            expect($url->toString())->toBe('https://example.com/test?search=paris');
        });

        it('handles non-existent keys', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=paris')
                ->except(['page', 'sort']);

            expect($url->toString())->toBe('https://example.com/test?search=paris');
        });
    });

    describe('generate et ordering', function () {
        it('removes empty values', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=&operationnel=1&page=&sort=nom');

            expect($url->toString())->toBe('https://example.com/test?operationnel=1&sort=nom');
        });

        it('orders parameters correctly: priority → business → technical', function () {
            $url = UrlBuilder::fromString('https://example.com/test?sort=nom&ville=paris&operationnel=1&page=2&direction=asc&recherche=cinema&perPage=25');

            expect($url->toString())->toBe('https://example.com/test?recherche=cinema&operationnel=1&ville=paris&page=2&perPage=25&sort=nom&direction=asc');
        });

        it('orders business parameters alphabetically', function () {
            $url = UrlBuilder::fromString('https://example.com/test?ville=paris&operationnel=1&pays=france');

            expect($url->toString())->toBe('https://example.com/test?operationnel=1&pays=france&ville=paris');
        });

        it('orders technical parameters in fixed order', function () {
            $url = UrlBuilder::fromString('https://example.com/test?direction=asc&sort=nom&perPage=25&page=2');

            expect($url->toString())->toBe('https://example.com/test?page=2&perPage=25&sort=nom&direction=asc');
        });

        it('puts recherche first among business parameters', function () {
            $url = UrlBuilder::fromString('https://example.com/test?ville=paris&operationnel=1&recherche=cinema&pays=france');

            expect($url->toString())->toBe('https://example.com/test?recherche=cinema&operationnel=1&pays=france&ville=paris');
        });
    });

    describe('fluent interface', function () {
        it('chains operations correctly', function () {
            $url = UrlBuilder::fromString('https://example.com/test?old=value&page=1&empty=')
                ->with('search', 'paris')
                ->with('operationnel', '1')
                ->remove('old')
                ->with('sort', 'nom');

            expect($url->toString())->toBe('https://example.com/test?operationnel=1&search=paris&page=1&sort=nom');
        });

        it('handles complex real-world scenario', function () {
            $url = UrlBuilder::fromString('https://example.com/cinemas?page=3&sort=ville&ville=&operationnel=0&search=')
                ->with('recherche', 'cinema')
                ->remove('search') // Remove old search param
                ->with('operationnel', '1')
                ->remove('page') // Reset pagination when changing filters
                ->with('direction', 'asc');

            expect($url->toString())->toBe('https://example.com/cinemas?recherche=cinema&operationnel=1&sort=ville&direction=asc');
        });
    });

    describe('edge cases', function () {
        it('handles URL without query parameters', function () {
            $url = UrlBuilder::fromString('https://example.com/test');

            expect($url->toString())->toBe('https://example.com/test');
        });
    });

    describe('toString and __toString', function () {
        it('toString calls generate automatically', function () {
            $url = UrlBuilder::fromString('https://example.com/test?sort=nom&recherche=test&page=2');

            expect($url->toString())->toBe('https://example.com/test?recherche=test&page=2&sort=nom');
        });

        it('__toString works correctly', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=paris');

            expect((string) $url)->toBe('https://example.com/test?search=paris');
        });
    });

    describe('toUrl', function () {
        it('returns underlying Spatie Url object', function () {
            $url = UrlBuilder::fromString('https://example.com/test?search=paris');

            expect($url->toUrl())->toBeInstanceOf(Url::class);
        });
    });
});
