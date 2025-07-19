# 🎬 Cinéphoria

**Application de gestion de cinéma multi-plateforme** - Projet ECF 2025 (RNCP 37873)

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/PostgreSQL-15-336791?style=for-the-badge&logo=postgresql&logoColor=white" />
  <img src="https://img.shields.io/badge/MongoDB-7.0-47A248?style=for-the-badge&logo=mongodb&logoColor=white" />
</div>

## 📋 Description

Cinéphoria est une application complète de gestion de cinéma pour une chaîne franco-belge de 7 établissements. Le projet suit une architecture Clean Architecture stricte avec Domain-Driven Design (DDD).

### 🎯 Fonctionnalités principales

- **Gestion des films** : Catalogue complet avec notes et critiques
- **Réservation en ligne** : Sélection de sièges interactifs
- **Multi-cinémas** : 7 établissements en France et Belgique
- **Billets QR Code** : Génération et validation automatique
- **Système de notation** : Évaluation des films par les clients
- **Analytics** : Tableaux de bord temps réel avec MongoDB


## 🚀 Installation

### Prérequis

- Docker Desktop
- Git

### Installation avec Laravel Sail

```bash
# Cloner le projet
git clone https://github.com/yourusername/cinephoria.git
cd cinephoria

# Installation initiale (utilise PHP du système temporairement)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# Configuration
cp .env.example .env

# Démarrer Sail (PostgreSQL, MongoDB, Redis inclus)
./vendor/bin/sail up -d

# Générer la clé d'application
./vendor/bin/sail artisan key:generate

# Installer les dépendances Node
./vendor/bin/sail npm install

# Bases de données
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan migrate:mongodb

# Compiler les assets
./vendor/bin/sail npm run build

# Pour le développement
./vendor/bin/sail npm run dev
```

## 📦 Technologies

### Backend
- **Laravel 12** - Framework PHP moderne
- **PostgreSQL** - Base de données relationnelle principale


### Frontend
- **Tailwind CSS v4** + **DaisyUI** - Design system cinéma
- **Alpine.js** - Interactivité sans SPA
- **Leaflet** + **OpenStreetMap** - Cartes interactives

### Packages clés
- **MoneyPHP** - Gestion précise des prix
- **cuyz/valinor** - DTOs type-safe
- **sqids** - Génération d'identifiants courts et uniques pour les URLs publiques


## 🧪 Tests

```bash
# Tous les tests
composer test

# Tests en parallèle
php artisan test --parallel
```

## 🔒 Sécurité

- **Authentification** : Multi-table avec types d'utilisateurs
- **Rate limiting** : Protection brute force

## 📄 Licence

Projet éducatif dans le cadre de la certification RNCP 37873.

---

<div align="center">
  <p>Développé avec ❤️ pour la certification Concepteur Développeur d'Applications</p>
  <p><strong>ECF 2025</strong></p>
</div>
