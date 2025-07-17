<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Billet de Réservation - {{ $reservation->numero_reservation }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
            position: relative;
        }
        
        .background-poster {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.1;
            z-index: -2;
        }
        
        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.9) 0%, rgba(30, 64, 175, 0.95) 100%);
            z-index: -1;
        }
        
        .ticket-container {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        
        .ticket-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        .ticket-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .ticket-number {
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .ticket-body {
            padding: 30px;
        }
        
        .film-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            text-align: center;
        }
        
        .film-poster-main {
            width: 450px;
            height: auto;
            border-radius: 8px;
            margin: 0 auto 20px auto;
            display: block;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .film-poster-placeholder {
            width: 450px;
            height: 200px;
            border-radius: 8px;
            margin: 0 auto 20px auto;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .poster-text {
            font-size: 16px;
            font-weight: bold;
            color: #6b7280;
            transform: rotate(-45deg);
        }
        
        .film-title {
            font-size: 28px;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 10px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .film-details {
            color: #6b7280;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        
        .info-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }
        
        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto 15px auto;
            display: block;
        }
        
        .qr-instructions {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
        }
        
        .price-section {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .price-total {
            font-size: 18px;
            font-weight: bold;
            color: #3b82f6;
            border-top: 2px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .instructions {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .instructions h3 {
            color: #92400e;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        
        .instructions ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 8px;
            font-size: 14px;
            color: #92400e;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    @if($reservation->seance->film->affiche_url)
        <div class="background-poster" style="background-image: url('{{ $reservation->seance->film->affiche_url }}');"></div>
    @endif
    <div class="background-overlay"></div>
    
    <div class="ticket-container">
        <!-- Header -->
        <div class="ticket-header">
            <h1>Billet de Réservation</h1>
            <div class="ticket-number">{{ $reservation->numero_reservation }}</div>
        </div>

        <!-- Body -->
        <div class="ticket-body">
            <!-- Film Section -->
            <div class="film-section">
                @if($reservation->seance->film->affiche_url)
                    <img 
                        src="{{ $reservation->seance->film->affiche_url }}" 
                        alt="Affiche du film" 
                        class="film-poster-main"
                    >
                @else
                    <div class="film-poster-placeholder">
                        <div class="poster-text">FILM</div>
                    </div>
                @endif
                
                <h2 class="film-title">{{ $reservation->seance->film->titre }}</h2>
                <div class="film-details">{{ $reservation->seance->film->categorie }} • {{ $reservation->seance->film->duree_minutes }} min</div>
                <div class="film-details">Statut: {{ $reservation->getStatutLabel() }}</div>
            </div>

            <!-- Information Grid -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Cinema</div>
                    <div class="info-value">{{ $reservation->seance->salle->cinema->nom }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Salle</div>
                    <div class="info-value">{{ $reservation->seance->salle->nom }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Date</div>
                    <div class="info-value">{{ $reservation->seance->date_heure_debut->format('d/m/Y') }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Heure</div>
                    <div class="info-value">{{ $reservation->seance->date_heure_debut->format('H\hi') }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Version</div>
                    <div class="info-value">{{ strtoupper($reservation->seance->version_linguistique ?? 'VF') }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Places</div>
                    <div class="info-value">{{ $reservation->nombre_places }}</div>
                </div>
            </div>

            <!-- Seats Section -->
            <div class="seats-section" style="text-align: center; margin-bottom: 30px; padding: 20px; background: #f8fafc; border-radius: 8px;">
                <div class="info-label" style="font-size: 14px; color: #6b7280; text-transform: uppercase; font-weight: bold; margin-bottom: 15px;">
                    Sièges réservés
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: center;">
                    @foreach($reservation->billets as $billet)
                        <span style="background: #3b82f6; color: white; padding: 6px 12px; border-radius: 16px; font-size: 14px; font-weight: bold;">
                            {{ $billet->place }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="qr-section">
                <img src="{{ $qrCodeDataUri }}" alt="QR Code" class="qr-code">
                <div class="qr-instructions">
                    <strong>Présentez ce QR code à l'entrée du cinéma</strong><br>
                    Ou communiquez votre numéro de réservation
                </div>
            </div>

            <!-- Price Section -->
            <div class="price-section">
                <div class="price-row">
                    <span>{{ $reservation->nombre_places }} place(s)</span>
                    <span>{{ number_format($reservation->prix_total, 2) }} €</span>
                </div>
                @if($reservation->montant_paye)
                    <div class="price-row">
                        <span>Montant payé</span>
                        <span>{{ number_format($reservation->montant_paye, 2) }} €</span>
                    </div>
                @endif
                <div class="price-row price-total">
                    <span>Total</span>
                    <span>{{ number_format($reservation->prix_total, 2) }} €</span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>Instructions importantes</h3>
                <ul>
                    <li>Présentez-vous à l'accueil 15 minutes avant le début de la séance</li>
                    <li>Conservez ce billet jusqu'à la fin de la séance</li>
                    <li>Les places sont garanties jusqu'à 10 minutes avant le début</li>
                    <li>Aucun remboursement après le début de la séance</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Réservation effectuée le {{ $reservation->created_at->format('d/m/Y à H\hi') }}<br>
            Cinéphoria - Votre cinéma de quartier
        </div>
    </div>
</body>
</html>