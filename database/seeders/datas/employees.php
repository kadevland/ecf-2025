<?php

declare(strict_types=1);

return [
    // Configuration par cinéma selon taille
    'cinema_configs' => [
        '550e8400-e29b-41d4-a716-446655440001' => ['name' => 'Châtelet', 'salles' => 8, 'employees' => 15], // Moyen
        '550e8400-e29b-41d4-a716-446655440002' => ['name' => 'La Défense', 'salles' => 10, 'employees' => 20], // Grand
        '550e8400-e29b-41d4-a716-446655440003' => ['name' => 'Lyon', 'salles' => 7, 'employees' => 13], // Moyen
        '550e8400-e29b-41d4-a716-446655440004' => ['name' => 'Marseille', 'salles' => 6, 'employees' => 10], // Petit
        '550e8400-e29b-41d4-a716-446655440005' => ['name' => 'Bruxelles', 'salles' => 9, 'employees' => 18], // Grand
        '550e8400-e29b-41d4-a716-446655440006' => ['name' => 'Bordeaux', 'salles' => 8, 'employees' => 16], // Moyen
        '550e8400-e29b-41d4-a716-446655440007' => ['name' => 'Toulouse', 'salles' => 6, 'employees' => 12], // Petit
    ],

    // Postes dans un cinéma avec répartition réaliste
    'positions' => [
        'manager'         => ['ratio' => 0.1, 'min' => 1], // 1 manager minimum
        'caissier'        => ['ratio' => 0.4, 'min' => 2], // 40% caissiers
        'projectionniste' => ['ratio' => 0.25, 'min' => 1], // 25% projectionnistes
        'maintenance'     => ['ratio' => 0.15, 'min' => 1], // 15% maintenance
        'accueil'         => ['ratio' => 0.1, 'min' => 1], // 10% accueil
    ],

    // Base de données noms français
    'prenoms' => [
        'masculin' => [
            'Pierre', 'Paul', 'Jacques', 'Jean', 'Michel', 'André', 'Philippe', 'Alain', 'Bernard', 'Christian',
            'Daniel', 'François', 'Gérard', 'Henri', 'Julien', 'Laurent', 'Marc', 'Nicolas', 'Olivier', 'Patrick',
            'Robert', 'Stéphane', 'Thierry', 'Vincent', 'Yves', 'Antoine', 'Bruno', 'Christophe', 'David', 'Eric',
            'Fabrice', 'Frédéric', 'Guillaume', 'Hervé', 'Jérôme', 'Lionel', 'Loïc', 'Ludovic', 'Maxime', 'Rémi',
            'Sébastien', 'Sylvain', 'Thomas', 'Xavier', 'Yann', 'Adrien', 'Alexandre', 'Arnaud', 'Benjamin', 'Cédric',
        ],
        'feminin' => [
            'Marie', 'Françoise', 'Catherine', 'Monique', 'Sylvie', 'Isabelle', 'Martine', 'Brigitte', 'Jacqueline', 'Nathalie',
            'Chantal', 'Christine', 'Nicole', 'Pascale', 'Véronique', 'Sandrine', 'Valérie', 'Corinne', 'Karine', 'Stéphanie',
            'Sophie', 'Patricia', 'Laurence', 'Carole', 'Emmanuelle', 'Céline', 'Virginie', 'Hélène', 'Agnès', 'Dominique',
            'Audrey', 'Caroline', 'Delphine', 'Elodie', 'Estelle', 'Fabienne', 'Ghislaine', 'Joëlle', 'Laure', 'Magali',
            'Muriel', 'Nadine', 'Odile', 'Sabrina', 'Solène', 'Tiphaine', 'Aurélie', 'Camille', 'Elise', 'Juliette',
        ],
    ],

    'noms' => [
        'Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit', 'Durand', 'Leroy', 'Moreau',
        'Simon', 'Laurent', 'Lefebvre', 'Michel', 'Garcia', 'David', 'Bertrand', 'Roux', 'Vincent', 'Fournier',
        'Morel', 'Girard', 'Andre', 'Lefevre', 'Mercier', 'Dupont', 'Lambert', 'Bonnet', 'Francois', 'Martinez',
        'Legrand', 'Garnier', 'Faure', 'Rousseau', 'Blanc', 'Guerin', 'Muller', 'Henry', 'Roussel', 'Nicolas',
        'Perrin', 'Morin', 'Mathieu', 'Clement', 'Gauthier', 'Dumont', 'Lopez', 'Fontaine', 'Chevalier', 'Robin',
        'Masson', 'Sanchez', 'Gerard', 'Nguyen', 'Boyer', 'Denis', 'Lemaire', 'Duval', 'Joly', 'Gautier',
        'Roger', 'Roche', 'Roy', 'Noel', 'Meyer', 'Lucas', 'Meunier', 'Jean', 'Perez', 'Marchand',
        'Dufour', 'Blanchard', 'Marie', 'Barbier', 'Brun', 'Dumas', 'Brunet', 'Schmitt', 'Leroux', 'Colin',
    ],

    // Domaines email professionnels
    'domains' => [
        'gmail.com', 'yahoo.fr', 'hotmail.fr', 'outlook.fr', 'orange.fr', 'free.fr', 'laposte.net', 'wanadoo.fr',
        'sfr.fr', 'bbox.fr', 'live.fr', 'msn.com', 'alice.fr', 'neuf.fr', 'club-internet.fr', 'numericable.fr',
    ],
];
