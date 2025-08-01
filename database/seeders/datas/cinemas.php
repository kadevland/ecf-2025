<?php

declare(strict_types=1);

return [
    [
        'uuid'        => '550e8400-e29b-41d4-a716-446655440001',
        'code_cinema' => 'CHT',
        'nom'         => 'Cinéphoria Châtelet',
        'description' => 'Cinéma historique au cœur de Paris, rénové avec les dernières technologies.',
        'adresse'     => [
            'rue'         => '1 Place du Châtelet',
            'code_postal' => '75001',
            'ville'       => 'Paris',
            'pays'        => 'FR',
        ],
        'coordonnees_gps' => [
            'latitude'  => 48.8606,
            'longitude' => 2.3467,
        ],
        'telephone'          => '0142361580',
        'email'              => 'chatelet@cinephoria.fr',
        'statut'             => 'actif',
        'horaires_ouverture' => [
            'lundi'    => ['10:00', '23:30'],
            'mardi'    => ['10:00', '23:30'],
            'mercredi' => ['10:00', '23:30'],
            'jeudi'    => ['10:00', '23:30'],
            'vendredi' => ['10:00', '01:00'],
            'samedi'   => ['10:00', '01:00'],
            'dimanche' => ['10:00', '23:30'],
        ],
        'services' => ['parking', 'restaurant', 'boutique', 'bar', 'accessibilite'],
    ],
    [
        'uuid'        => '550e8400-e29b-41d4-a716-446655440002',
        'code_cinema' => 'DEF',
        'nom'         => 'Cinéphoria La Défense',
        'description' => 'Complexe moderne de 12 salles dans le quartier d\'affaires.',
        'adresse'     => [
            'rue'         => '4 Place de la Défense',
            'code_postal' => '92400',
            'ville'       => 'Courbevoie',
            'pays'        => 'FR',
        ],
        'coordonnees_gps' => [
            'latitude'  => 48.8917,
            'longitude' => 2.2369,
        ],
        'telephone'          => '0147752530',
        'email'              => 'ladefense@cinephoria.fr',
        'statut'             => 'actif',
        'horaires_ouverture' => [
            'lundi'    => ['11:00', '23:00'],
            'mardi'    => ['11:00', '23:00'],
            'mercredi' => ['11:00', '23:00'],
            'jeudi'    => ['11:00', '23:00'],
            'vendredi' => ['11:00', '01:00'],
            'samedi'   => ['10:00', '01:00'],
            'dimanche' => ['10:00', '23:00'],
        ],
        'services' => ['parking', 'restaurant', 'boutique', 'imax', 'dolby_atmos'],
    ],
    [
        'uuid'        => '550e8400-e29b-41d4-a716-446655440003',
        'code_cinema' => 'LYO',
        'nom'         => 'Cinéphoria Lyon Presqu\'île',
        'description' => 'Cinéma d\'art et d\'essai et blockbusters au cœur de Lyon.',
        'adresse'     => [
            'rue'         => '15 Rue de la République',
            'code_postal' => '69002',
            'ville'       => 'Lyon',
            'pays'        => 'FR',
        ],
        'coordonnees_gps' => [
            'latitude'  => 45.7640,
            'longitude' => 4.8357,
        ],
        'telephone'          => '0478371245',
        'email'              => 'lyon@cinephoria.fr',
        'statut'             => 'actif',
        'horaires_ouverture' => [
            'lundi'    => ['14:00', '22:30'],
            'mardi'    => ['14:00', '22:30'],
            'mercredi' => ['10:00', '22:30'],
            'jeudi'    => ['14:00', '22:30'],
            'vendredi' => ['14:00', '23:30'],
            'samedi'   => ['10:00', '23:30'],
            'dimanche' => ['10:00', '22:30'],
        ],
        'services' => ['bar', 'boutique', 'accessibilite'],
    ],
    [
        'uuid'        => '550e8400-e29b-41d4-a716-446655440004',
        'code_cinema' => 'MAR',
        'nom'         => 'Cinéphoria Marseille Vieux-Port',
        'description' => 'Vue imprenable sur le Vieux-Port, ambiance méditerranéenne.',
        'adresse'     => [
            'rue'         => '33 Quai du Port',
            'code_postal' => '13002',
            'ville'       => 'Marseille',
            'pays'        => 'FR',
        ],
        'coordonnees_gps' => [
            'latitude'  => 43.2965,
            'longitude' => 5.3698,
        ],
        'telephone'          => '0491547890',
        'email'              => 'marseille@cinephoria.fr',
        'statut'             => 'actif',
        'horaires_ouverture' => [
            'lundi'    => ['14:00', '22:00'],
            'mardi'    => ['14:00', '22:00'],
            'mercredi' => ['14:00', '22:00'],
            'jeudi'    => ['14:00', '22:00'],
            'vendredi' => ['14:00', '23:00'],
            'samedi'   => ['10:00', '23:00'],
            'dimanche' => ['10:00', '22:00'],
        ],
        'services' => ['terrasse', 'bar', 'accessibilite'],
    ],
    [
        'uuid'        => '550e8400-e29b-41d4-a716-446655440005',
        'code_cinema' => 'BRU',
        'nom'         => 'Cinéphoria Bruxelles Centre',
        'description' => 'Premier cinéma Cinéphoria en Belgique, près de la Grand-Place.',
        'adresse'     => [
            'rue'         => '25 Rue Neuve',
            'code_postal' => '1000',
            'ville'       => 'Bruxelles',
            'pays'        => 'BE',
        ],
        'coordonnees_gps' => [
            'latitude'  => 50.8503,
            'longitude' => 4.3517,
        ],
        'telephone'          => '025123456',
        'email'              => 'bruxelles@cinephoria.be',
        'statut'             => 'actif',
        'horaires_ouverture' => [
            'lundi'    => ['14:00', '22:30'],
            'mardi'    => ['14:00', '22:30'],
            'mercredi' => ['14:00', '22:30'],
            'jeudi'    => ['14:00', '22:30'],
            'vendredi' => ['14:00', '23:30'],
            'samedi'   => ['10:00', '23:30'],
            'dimanche' => ['10:00', '22:30'],
        ],
        'services' => ['parking', 'restaurant', 'boutique', 'accessibilite'],
    ],
    [
        'uuid'        => '550e8400-e29b-41d4-a716-446655440006',
        'code_cinema' => 'BOR',
        'nom'         => 'Cinéphoria Bordeaux Mériadeck',
        'description' => 'Complexe moderne dans le quartier Mériadeck.',
        'adresse'     => [
            'rue'         => '12 Cours du Maréchal Juin',
            'code_postal' => '33000',
            'ville'       => 'Bordeaux',
            'pays'        => 'FR',
        ],
        'coordonnees_gps' => [
            'latitude'  => 44.8378,
            'longitude' => -0.5792,
        ],
        'telephone'          => '0556442870',
        'email'              => 'bordeaux@cinephoria.fr',
        'statut'             => 'actif',
        'horaires_ouverture' => [
            'lundi'    => ['14:00', '22:30'],
            'mardi'    => ['14:00', '22:30'],
            'mercredi' => ['14:00', '22:30'],
            'jeudi'    => ['14:00', '22:30'],
            'vendredi' => ['14:00', '23:00'],
            'samedi'   => ['10:00', '23:00'],
            'dimanche' => ['10:00', '22:30'],
        ],
        'services' => ['parking', 'bar', 'boutique', 'dolby_atmos'],
    ],
    [
        'uuid'        => '550e8400-e29b-41d4-a716-446655440007',
        'code_cinema' => 'TOU',
        'nom'         => 'Cinéphoria Toulouse Capitole',
        'description' => 'Cinéma de prestige face au Capitole, architecture du XVIIIe siècle.',
        'adresse'     => [
            'rue'         => '8 Place du Capitole',
            'code_postal' => '31000',
            'ville'       => 'Toulouse',
            'pays'        => 'FR',
        ],
        'coordonnees_gps' => [
            'latitude'  => 43.6047,
            'longitude' => 1.4442,
        ],
        'telephone'          => '0561234567',
        'email'              => 'toulouse@cinephoria.fr',
        'statut'             => 'actif',
        'horaires_ouverture' => [
            'lundi'    => ['14:00', '22:30'],
            'mardi'    => ['14:00', '22:30'],
            'mercredi' => ['14:00', '22:30'],
            'jeudi'    => ['14:00', '22:30'],
            'vendredi' => ['14:00', '23:00'],
            'samedi'   => ['10:00', '23:00'],
            'dimanche' => ['10:00', '22:30'],
        ],
        'services' => ['bar', 'boutique', 'patrimoine_historique'],
    ],
];
