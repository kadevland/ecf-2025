<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;

// Calcul des périodes mercredi à mercredi (-1 à +2 périodes)
$today              = CarbonImmutable::now();
$lastWednesday      = $today->previous(CarbonImmutable::WEDNESDAY);
$currentPeriodStart = $lastWednesday;

$periods = [];
for ($i = -1; $i <= 2; $i++) {
    $periodStart = $currentPeriodStart->addWeeks($i);
    $periodEnd   = $periodStart->addDays(6); // Mardi suivant
    $periods[]   = [
        'start' => $periodStart,
        'end'   => $periodEnd,
        'label' => 'Semaine du '.$periodStart->format('d/m/Y'),
    ];
}

return [
    'periods' => $periods,

    // Configuration cinémas avec leurs capacités et qualités
    'cinemas' => [
        '550e8400-e29b-41d4-a716-446655440001' => [ // Châtelet
            'name'            => 'Châtelet',
            'qualites'        => ['standard', '4k', '3d', 'dolby_atmos'],
            'films_selection' => 'all', // Ce cinéma peut programmer tous les films
            'salles'          => [
                '550e8400-e29b-41d4-a716-446655440101', // Salle Méliès
                '550e8400-e29b-41d4-a716-446655440102', // Salle Lumière
                '550e8400-e29b-41d4-a716-446655440103', // Salle Godard
                '550e8400-e29b-41d4-a716-446655440104', // Salle Truffaut
                '550e8400-e29b-41d4-a716-446655440105', // Salle Renoir
                '550e8400-e29b-41d4-a716-446655440106', // Salle Rohmer
                '550e8400-e29b-41d4-a716-446655440107', // Salle Varda
                '550e8400-e29b-41d4-a716-446655440108', // Salle Demy
            ],
        ],
        '550e8400-e29b-41d4-a716-446655440002' => [ // La Défense
            'name'            => 'La Défense',
            'qualites'        => ['standard', '4k', 'imax', '3d', 'dolby_atmos'],
            'films_selection' => 'all', // Programme tous les films avec focus sur les blockbusters
            'salles'          => [
                '550e8400-e29b-41d4-a716-446655440201', // Salle IMAX
                '550e8400-e29b-41d4-a716-446655440202', // Salle Premium
                '550e8400-e29b-41d4-a716-446655440203', // Salle Alpha
                '550e8400-e29b-41d4-a716-446655440204', // Salle Beta
                '550e8400-e29b-41d4-a716-446655440205', // Salle Gamma
                '550e8400-e29b-41d4-a716-446655440206', // Salle Delta
                '550e8400-e29b-41d4-a716-446655440207', // Salle Epsilon
                '550e8400-e29b-41d4-a716-446655440208', // Salle Zeta
                '550e8400-e29b-41d4-a716-446655440209', // Salle Eta
                '550e8400-e29b-41d4-a716-446655440210', // Salle Theta
            ],
        ],
        '550e8400-e29b-41d4-a716-446655440003' => [ // Lyon
            'name'            => 'Lyon',
            'qualites'        => ['standard', '4k', '3d', 'dolby_atmos'],
            'films_selection' => 'all', // Programme tous les films
            'salles'          => [
                '550e8400-e29b-41d4-a716-446655440301', // Salle Presqu'île
                '550e8400-e29b-41d4-a716-446655440302', // Salle Bellecour
                '550e8400-e29b-41d4-a716-446655440303', // Salle Croix-Rousse
                '550e8400-e29b-41d4-a716-446655440304', // Salle Vieux-Lyon
                '550e8400-e29b-41d4-a716-446655440305', // Salle Confluence
                '550e8400-e29b-41d4-a716-446655440306', // Salle Part-Dieu
                '550e8400-e29b-41d4-a716-446655440307', // Salle Fourvière
            ],
        ],
        '550e8400-e29b-41d4-a716-446655440004' => [ // Marseille
            'name'            => 'Marseille',
            'qualites'        => ['standard', '4k', '3d'],
            'films_selection' => 'all', // Programme tous les films
            'salles'          => [
                '550e8400-e29b-41d4-a716-446655440005', // Cocorico
                '550e8400-e29b-41d4-a716-446655440013', // Moi Moche et Méchant 4
                '550e8400-e29b-41d4-a716-446655440035', // Astérix Cléopâtre
                '550e8400-e29b-41d4-a716-446655440036', // Hereditary
                '550e8400-e29b-41d4-a716-446655440037', // Get Out
                '550e8400-e29b-41d4-a716-446655440041', // Marriage Story
                '550e8400-e29b-41d4-a716-446655440047', // Django
            ],
            'salles' => [
                '550e8400-e29b-41d4-a716-446655440401', // Salle Vieux-Port
                '550e8400-e29b-41d4-a716-446655440402', // Salle Canebière
                '550e8400-e29b-41d4-a716-446655440403', // Salle Notre-Dame
                '550e8400-e29b-41d4-a716-446655440404', // Salle Panier
                '550e8400-e29b-41d4-a716-446655440405', // Salle Corniche
                '550e8400-e29b-41d4-a716-446655440406', // Salle Calanques
            ],
        ],
        '550e8400-e29b-41d4-a716-446655440005' => [ // Bruxelles
            'name'            => 'Bruxelles',
            'qualites'        => ['standard', '4k', '3d', 'dolby_atmos'],
            'films_selection' => 'all', // Programme tous les films
            'salles'          => [
                '550e8400-e29b-41d4-a716-446655440501', // Salle Grand-Place
                '550e8400-e29b-41d4-a716-446655440502', // Salle Atomium
                '550e8400-e29b-41d4-a716-446655440503', // Salle Manneken-Pis
                '550e8400-e29b-41d4-a716-446655440504', // Salle Sablon
                '550e8400-e29b-41d4-a716-446655440505', // Salle Marolles
                '550e8400-e29b-41d4-a716-446655440506', // Salle Ixelles
                '550e8400-e29b-41d4-a716-446655440507', // Salle Etterbeek
                '550e8400-e29b-41d4-a716-446655440508', // Salle Uccle
                '550e8400-e29b-41d4-a716-446655440509', // Salle Laeken
            ],
        ],
        '550e8400-e29b-41d4-a716-446655440006' => [ // Bordeaux
            'name'            => 'Bordeaux',
            'qualites'        => ['standard', '4k', '3d', 'dolby_atmos'],
            'films_selection' => 'all', // Programme tous les films
            'salles'          => [
                '550e8400-e29b-41d4-a716-446655440601', // Salle Garonne
                '550e8400-e29b-41d4-a716-446655440602', // Salle Mériadeck
                '550e8400-e29b-41d4-a716-446655440603', // Salle Saint-Pierre
                '550e8400-e29b-41d4-a716-446655440604', // Salle Bastide
                '550e8400-e29b-41d4-a716-446655440605', // Salle Chartrons
                '550e8400-e29b-41d4-a716-446655440606', // Salle Caudéran
                '550e8400-e29b-41d4-a716-446655440607', // Salle Pessac
                '550e8400-e29b-41d4-a716-446655440608', // Salle Mérignac
            ],
        ],
        '550e8400-e29b-41d4-a716-446655440007' => [ // Toulouse
            'name'            => 'Toulouse',
            'qualites'        => ['standard', '4k'],
            'films_selection' => 'all', // Programme tous les films
            'salles'          => [
                '550e8400-e29b-41d4-a716-446655440701', // Salle Capitole
                '550e8400-e29b-41d4-a716-446655440702', // Salle Wilson
                '550e8400-e29b-41d4-a716-446655440703', // Salle Esquirol
                '550e8400-e29b-41d4-a716-446655440704', // Salle Jeanne-d'Arc
                '550e8400-e29b-41d4-a716-446655440705', // Salle Saint-Sernin
                '550e8400-e29b-41d4-a716-446655440706', // Salle Jacobins
            ],
        ],
    ],

    // Grilles horaires par type de jour
    'horaires' => [
        'lundi_mardi_mercredi_jeudi' => [
            '10:00', '12:30', '14:00', '16:30', '19:00', '21:30',
        ],
        'vendredi' => [
            '10:00', '12:30', '14:00', '16:30', '19:00', '21:30', '23:45',
        ],
        'samedi' => [
            '09:30', '11:00', '13:30', '15:00', '17:30', '19:00', '21:30', '23:45',
        ],
        'dimanche' => [
            '10:00', '12:30', '14:00', '16:30', '19:00', '21:30',
        ],
    ],

    // Règles de qualité selon salle et horaire
    'qualite_rules' => [
        'imax' => [
            'salles'      => ['550e8400-e29b-41d4-a716-446655440201'], // Salle IMAX La Défense
            'horaires'    => ['19:00', '21:30'],
            'films_types' => ['science_fiction', 'action', 'aventure'],
        ],
        '3d' => [
            'horaires'    => ['14:00', '16:30', '19:00'],
            'films_types' => ['animation', 'action', 'science_fiction'],
        ],
        'dolby_atmos' => [
            'horaires'    => ['21:30', '23:45'],
            'films_types' => ['action', 'science_fiction', 'thriller'],
        ],
        '4k' => [
            'horaires'    => ['19:00', '21:30'],
            'films_types' => ['drame', 'thriller', 'science_fiction'],
        ],
    ],

    // Tarifs par qualité
    'tarifs' => [
        'standard'    => 9.50,
        '4k'          => 12.00,
        'imax'        => 15.50,
        '3d'          => 13.50,
        'dolby_atmos' => 14.00,
    ],

    // Versions disponibles
    'versions' => [
        'VF'   => 0.7,  // 70% VF
        'VO'   => 0.2,  // 20% VO
        'VOST' => 0.1, // 10% VOST
    ],
];
