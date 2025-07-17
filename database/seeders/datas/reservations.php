<?php

declare(strict_types=1);

return [
    // Répartition des statuts (pourcentages)
    'statuts_distribution' => [
        'payee'      => 0.6,      // 60% payées (utilisation normale)
        'confirmee'  => 0.15,  // 15% confirmées (en attente paiement)
        'terminee'   => 0.1,    // 10% terminées (déjà utilisées)
        'annulee'    => 0.1,     // 10% annulées
        'en_attente' => 0.04, // 4% en attente
        'expiree'    => 0.01,    // 1% expirées
    ],

    // Types de tarifs avec leur répartition
    'tarifs_distribution' => [
        'plein'    => 0.5,      // 50% plein tarif
        'reduit'   => 0.2,     // 20% réduit
        'etudiant' => 0.15,  // 15% étudiant
        'senior'   => 0.1,     // 10% senior
        'enfant'   => 0.05,    // 5% enfant
    ],

    // Nombre de places par réservation (probabilités)
    'places_distribution' => [
        1 => 0.3,  // 30% solo
        2 => 0.4,  // 40% couple
        3 => 0.15, // 15% famille/amis
        4 => 0.1,  // 10% groupe
        5 => 0.03, // 3% grand groupe
        6 => 0.02, // 2% très grand groupe
    ],

    // Patterns places de cinéma
    'patterns_places' => [
        'rangees'    => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'],
        'sieges_max' => 25, // Max 25 sièges par rangée
        'pmr'        => ['PMR-01', 'PMR-02', 'PMR-03', 'PMR-04'], // Places PMR
        'vip'        => ['VIP-01', 'VIP-02', 'VIP-03', 'VIP-04'], // Places VIP
    ],

    // Raisons d'annulation
    'raisons_annulation' => [
        'Annulation par le client',
        'Problème technique séance',
        'Maladie',
        'Changement de programme',
        'Remboursement demandé',
        'Séance reportée',
        'Problème de paiement',
        'Doublon réservation',
    ],

    // Notes spéciales
    'notes_speciales' => [
        'Réservation VIP',
        'Groupe scolaire',
        'Anniversaire',
        'Événement spécial',
        'Client fidèle',
        'Première réservation',
        'Réservation de dernière minute',
        'Réservation anticipée',
    ],

    // Domaines QR code
    'qr_domains' => [
        'https://tickets.cinephoria.fr',
        'https://qr.cinephoria.fr',
        'https://app.cinephoria.fr',
    ],

    // Nombre de réservations à créer par cinéma
    'reservations_par_cinema' => [
        '550e8400-e29b-41d4-a716-446655440001' => 45, // Châtelet
        '550e8400-e29b-41d4-a716-446655440002' => 60, // La Défense (plus grand)
        '550e8400-e29b-41d4-a716-446655440003' => 35, // Lyon
        '550e8400-e29b-41d4-a716-446655440004' => 25, // Marseille
        '550e8400-e29b-41d4-a716-446655440005' => 40, // Bruxelles
        '550e8400-e29b-41d4-a716-446655440006' => 30, // Bordeaux
        '550e8400-e29b-41d4-a716-446655440007' => 20, // Toulouse
    ],
];
