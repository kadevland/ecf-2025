<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Arr;
use Spatie\Url\Url;

final class UrlBuilder
{
    /** @var array<string> Paramètres métier prioritaires (ordre fixe) */
    private const PRIORITY_PARAMETERS = [
        'recherche',
    ];

    /** @var array<string> Paramètres techniques avec ordre fixe */
    private const TECHNICAL_PARAMETERS = [
        'page',
        'perPage',
        'sort',
        'direction',
    ];

    private Url $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * Alias pour toString()
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Crée un UrlBuilder à partir de l'URL courante
     */
    public static function current(): self
    {
        return new self(Url::fromString(request()->fullUrl()));
    }

    /**
     * Crée un UrlBuilder à partir d'une URL donnée
     */
    public static function fromString(string $url): self
    {
        return new self(Url::fromString($url));
    }

    /**
     * Ajoute ou modifie un paramètre
     */
    public function with(string $key, mixed $value): self
    {
        $clone = clone $this;

        // Convertir la valeur en string de manière sécurisée
        $stringValue = match (true) {
            is_string($value)  => $value,
            is_numeric($value) => (string) $value,
            is_bool($value)    => $value ? '1' : '0',
            is_null($value)    => '',
            default            => '',
        };

        $clone->url = $this->url->withQueryParameter($key, $stringValue);

        return $clone;
    }

    /**
     * Supprime un paramètre
     */
    public function remove(string $key): self
    {
        $clone      = clone $this;
        $clone->url = $this->url->withoutQueryParameter($key);

        return $clone;
    }

    /**
     * Garde seulement les paramètres spécifiés
     *
     * @param  array<string>  $keys
     */
    public function only(array $keys): self
    {
        $currentParams  = $this->url->getAllQueryParameters();
        $filteredParams = Arr::only($currentParams, $keys);

        $clone      = clone $this;
        $clone->url = $this->url->withQuery(http_build_query($filteredParams));

        return $clone;
    }

    /**
     * Supprime les paramètres spécifiés
     *
     * @param  array<string>  $keys
     */
    public function except(array $keys): self
    {
        $currentParams  = $this->url->getAllQueryParameters();
        $filteredParams = Arr::except($currentParams, $keys);

        $clone      = clone $this;
        $clone->url = $this->url->withQuery(http_build_query($filteredParams));

        return $clone;
    }

    /**
     * Nettoie les paramètres vides et ordonne selon notre logique métier
     */
    public function generate(): self
    {
        $params = $this->url->getAllQueryParameters();

        // Supprimer les valeurs vides
        $params = array_filter($params, function ($value) {
            return $value !== null && $value !== '' && $value !== [];
        });

        // Séparer paramètres métier prioritaires, métier et techniques
        $priorityParams  = [];
        $businessParams  = [];
        $technicalParams = [];

        foreach ($params as $key => $value) {
            if (in_array($key, self::PRIORITY_PARAMETERS, true)) {
                $priorityParams[$key] = $value;
            } elseif (in_array($key, self::TECHNICAL_PARAMETERS, true)) {
                $technicalParams[$key] = $value;
            } else {
                $businessParams[$key] = $value;
            }
        }

        // Ordonner les paramètres prioritaires selon PRIORITY_PARAMETERS
        $orderedPriorityParams = [];
        foreach (self::PRIORITY_PARAMETERS as $key) {
            if (array_key_exists($key, $priorityParams)) {
                $orderedPriorityParams[$key] = $priorityParams[$key];
            }
        }

        // Ordonner les paramètres métier alphabétiquement
        ksort($businessParams);

        // Ordonner les paramètres techniques selon TECHNICAL_PARAMETERS
        $orderedTechnicalParams = [];
        foreach (self::TECHNICAL_PARAMETERS as $key) {
            if (array_key_exists($key, $technicalParams)) {
                $orderedTechnicalParams[$key] = $technicalParams[$key];
            }
        }

        // Combiner : priorité → métier → technique
        $orderedParams = array_merge($orderedPriorityParams, $businessParams, $orderedTechnicalParams);

        $clone      = clone $this;
        $clone->url = $this->url->withQuery(http_build_query($orderedParams));

        return $clone;
    }

    /**
     * Convertit en string avec nettoyage automatique
     */
    public function toString(): string
    {
        return $this->generate()->url->__toString();
    }

    /**
     * Récupère l'URL Spatie sous-jacente
     */
    public function toUrl(): Url
    {
        return $this->url;
    }
}
