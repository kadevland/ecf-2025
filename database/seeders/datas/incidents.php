<?php

declare(strict_types=1);

return [
    // Configuration cinémas (UUID => nb_incidents)
    'cinema_distribution' => [
        '550e8400-e29b-41d4-a716-446655440001' => 6, // Châtelet
        '550e8400-e29b-41d4-a716-446655440002' => 8, // La Défense (plus grand)
        '550e8400-e29b-41d4-a716-446655440003' => 5, // Lyon
        '550e8400-e29b-41d4-a716-446655440004' => 4, // Marseille
        '550e8400-e29b-41d4-a716-446655440005' => 7, // Bruxelles
        '550e8400-e29b-41d4-a716-446655440006' => 5, // Bordeaux
        '550e8400-e29b-41d4-a716-446655440007' => 3, // Toulouse
    ],

    // Types d'incidents avec exemples réalistes
    'incidents_template' => [
        'projection' => [
            [
                'titre'        => 'Panne projecteur salle {salle}',
                'description'  => 'Le projecteur de la salle {salle} ne s\'allume plus. Écran noir pendant la séance de {heure}.',
                'localisation' => 'Salle {salle} - Régie projection',
                'priorite'     => 'elevee',
                'solution'     => 'Remplacement de la lampe du projecteur défectueuse. Test effectué avec succès.',
                'commentaires' => 'Lampe en fin de vie (plus de 2000h d\'utilisation). Commande de lampes de rechange effectuée.',
            ],
            [
                'titre'        => 'Problème climatisation salle {salle}',
                'description'  => 'La climatisation de la salle {salle} ne fonctionne plus. Température trop élevée signalée par les clients.',
                'localisation' => 'Salle {salle} - Système CVC',
                'priorite'     => 'normale',
                'solution'     => 'Nettoyage des filtres et redémarrage du système. Vérification des capteurs de température.',
                'commentaires' => 'Filtres très encrassés. Planifier un nettoyage mensuel.',
            ],
            [
                'titre'        => 'Dysfonctionnement système son salle {salle}',
                'description'  => 'Audio intermittent pendant la projection. Clients se plaignent de coupures sonores.',
                'localisation' => 'Salle {salle} - Système audio',
                'priorite'     => 'elevee',
                'solution'     => 'Resserrage des connexions audio et mise à jour du firmware du processeur audio.',
                'commentaires' => 'Problème récurrent. Envisager le remplacement du processeur audio.',
            ],
            [
                'titre'        => 'Panne éclairage d\'urgence',
                'description'  => 'Les éclairages de sécurité de la salle {salle} ne s\'allument pas lors du test hebdomadaire.',
                'localisation' => 'Salle {salle} - Éclairage sécurité',
                'priorite'     => 'critique',
                'solution'     => 'Remplacement des batteries défectueuses du système d\'éclairage de sécurité.',
                'commentaires' => 'Non-conformité sécurité. Intervention prioritaire effectuée.',
            ],
            [
                'titre'        => 'Problème système billetterie',
                'description'  => 'La borne de billetterie automatique n°{numero} ne fonctionne plus. Écran figé.',
                'localisation' => 'Hall d\'accueil - Borne {numero}',
                'priorite'     => 'normale',
                'solution'     => 'Redémarrage du système et mise à jour du logiciel de billetterie.',
                'commentaires' => 'Problème logiciel. Surveillance renforcée les prochains jours.',
            ],
        ],
        'securite' => [
            [
                'titre'        => 'Problème alarme incendie',
                'description'  => 'Déclenchement intempestif de l\'alarme incendie dans la salle {salle} à {heure}.',
                'localisation' => 'Salle {salle} - Détecteur incendie',
                'priorite'     => 'critique',
                'solution'     => 'Remplacement du détecteur de fumée défaillant. Test complet du système d\'alarme.',
                'commentaires' => 'Détecteur en fin de vie. Planifier le remplacement des autres détecteurs anciens.',
            ],
            [
                'titre'        => 'Caméra de surveillance HS',
                'description'  => 'La caméra de surveillance n°{numero} ne fonctionne plus. Angle mort dans la surveillance.',
                'localisation' => 'Hall d\'accueil - Caméra {numero}',
                'priorite'     => 'normale',
                'solution'     => 'Remplacement de la caméra défectueuse. Réglage de l\'angle de vue.',
                'commentaires' => 'Caméra endommagée par l\'humidité. Vérifier l\'étanchéité des autres caméras.',
            ],
            [
                'titre'        => 'Problème contrôle d\'accès',
                'description'  => 'Le système de contrôle d\'accès des employés ne reconnaît plus les badges.',
                'localisation' => 'Entrée personnel - Lecteur badges',
                'priorite'     => 'elevee',
                'solution'     => 'Nettoyage du lecteur et reconfiguration du système. Test avec tous les badges.',
                'commentaires' => 'Problème de saleté sur le lecteur. Nettoyage régulier nécessaire.',
            ],
            [
                'titre'        => 'Issue de secours bloquée',
                'description'  => 'La porte de secours de la salle {salle} ne s\'ouvre plus correctement.',
                'localisation' => 'Salle {salle} - Issue de secours',
                'priorite'     => 'critique',
                'solution'     => 'Réparation du mécanisme de la porte et graissage des charnières.',
                'commentaires' => 'Problème de sécurité majeur. Intervention immédiate effectuée.',
            ],
        ],
        'nettoyage' => [
            [
                'titre'        => 'Problème évacuation toilettes',
                'description'  => 'Les toilettes {genre} du niveau {niveau} sont bouchées. Eau stagnante.',
                'localisation' => 'Toilettes {genre} - Niveau {niveau}',
                'priorite'     => 'elevee',
                'solution'     => 'Débouchage des canalisations et désinfection complète des toilettes.',
                'commentaires' => 'Problème récurrent. Sensibiliser les clients à un usage approprié.',
            ],
            [
                'titre'        => 'Distributeur de boissons en panne',
                'description'  => 'Le distributeur automatique n°{numero} ne rend plus la monnaie.',
                'localisation' => 'Hall d\'accueil - Distributeur {numero}',
                'priorite'     => 'faible',
                'solution'     => 'Vidange du monnayeur et réapprovisionnement. Test de fonctionnement.',
                'commentaires' => 'Monnayeur plein. Planifier des vidanges plus fréquentes.',
            ],
            [
                'titre'        => 'Nettoyage salle insuffisant',
                'description'  => 'La salle {salle} n\'a pas été correctement nettoyée après la séance. Détritus au sol.',
                'localisation' => 'Salle {salle} - Rangées centrales',
                'priorite'     => 'normale',
                'solution'     => 'Nettoyage complet de la salle et rappel des consignes à l\'équipe de nettoyage.',
                'commentaires' => 'Séance avec forte affluence. Ajuster les temps de nettoyage.',
            ],
            [
                'titre'        => 'Problème ventilation hall',
                'description'  => 'Odeurs désagréables dans le hall d\'accueil. Ventilation insuffisante.',
                'localisation' => 'Hall d\'accueil - Système ventilation',
                'priorite'     => 'normale',
                'solution'     => 'Nettoyage des gaines de ventilation et remplacement des filtres.',
                'commentaires' => 'Filtres très encrassés. Maintenance préventive à planifier.',
            ],
        ],
        'autre' => [
            [
                'titre'        => 'Problème parking',
                'description'  => 'La barrière du parking ne se lève plus. Clients bloqués à l\'entrée.',
                'localisation' => 'Parking - Barrière d\'entrée',
                'priorite'     => 'elevee',
                'solution'     => 'Réparation du moteur de la barrière et test du système de reconnaissance.',
                'commentaires' => 'Usure normale. Prévoir maintenance préventive mensuelle.',
            ],
            [
                'titre'        => 'Ascenseur en panne',
                'description'  => 'L\'ascenseur principal est bloqué entre les étages. Personnes coincées.',
                'localisation' => 'Hall principal - Ascenseur',
                'priorite'     => 'critique',
                'solution'     => 'Intervention du technicien ascenseur. Évacuation des personnes et réparation.',
                'commentaires' => 'Panne électrique. Maintenance préventive renforcée nécessaire.',
            ],
            [
                'titre'        => 'Problème chauffage',
                'description'  => 'Le système de chauffage du hall d\'accueil ne fonctionne plus. Température trop basse.',
                'localisation' => 'Hall d\'accueil - Chauffage',
                'priorite'     => 'normale',
                'solution'     => 'Réparation de la chaudière et purge du circuit de chauffage.',
                'commentaires' => 'Problème de pression dans le circuit. Vérification complète effectuée.',
            ],
            [
                'titre'        => 'Dysfonctionnement Wi-Fi',
                'description'  => 'Le réseau Wi-Fi gratuit pour les clients ne fonctionne plus.',
                'localisation' => 'Ensemble du bâtiment - Réseau Wi-Fi',
                'priorite'     => 'faible',
                'solution'     => 'Redémarrage des bornes Wi-Fi et mise à jour du firmware.',
                'commentaires' => 'Problème de configuration. Surveillance de la stabilité du réseau.',
            ],
        ],
    ],

    // Emplacements typiques
    'localisations' => [
        'Salle'     => ['Salle Méliès', 'Salle Lumière', 'Salle IMAX', 'Salle Premium', 'Salle Alpha'],
        'Hall'      => ['Hall d\'accueil', 'Hall principal', 'Lobby'],
        'Toilettes' => ['Toilettes hommes', 'Toilettes femmes', 'Toilettes PMR'],
        'Technique' => ['Régie projection', 'Local technique', 'Régie son'],
        'Extérieur' => ['Parking', 'Terrasse', 'Entrée principale'],
    ],

    // Plages horaires réalistes
    'horaires' => [
        '09:30', '10:15', '11:00', '12:30', '14:00', '15:45', '17:30', '19:15', '20:00', '21:45', '22:30',
    ],

    // Niveaux
    'niveaux' => ['RDC', '1er étage', '2ème étage', 'Sous-sol'],

    // Genres pour toilettes
    'genres' => ['hommes', 'femmes', 'PMR'],

    // Numéros d'équipements
    'numeros' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
];
