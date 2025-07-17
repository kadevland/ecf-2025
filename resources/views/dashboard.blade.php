<x-admin.layout title="Dashboard">
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Films -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Total Films</h3>
                        <p class="text-3xl font-bold text-primary">142</p>
                    </div>
                    <div class="text-primary text-3xl">🎬</div>
                </div>
            </div>
        </div>

        <!-- Séances du jour -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Séances du jour</h3>
                        <p class="text-3xl font-bold text-success">28</p>
                    </div>
                    <div class="text-success text-3xl">🎭</div>
                </div>
            </div>
        </div>

        <!-- Réservations -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Réservations</h3>
                        <p class="text-3xl font-bold text-warning">1,234</p>
                    </div>
                    <div class="text-warning text-3xl">🎫</div>
                </div>
            </div>
        </div>

        <!-- Revenus du mois -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Revenus (€)</h3>
                        <p class="text-3xl font-bold text-error">89,450</p>
                    </div>
                    <div class="text-error text-3xl">💰</div>
                </div>
            </div>
        </div>
        </div>

        <!-- Navigation rapide -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Gestion Films -->
        <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow">
            <div class="card-body">
                <h2 class="card-title text-primary">
                    🎬 Gestion Films
                </h2>
                <p class="text-base-content/70">Consulter et gérer le catalogue de films</p>
                <div class="card-actions justify-end">
                    <a href="{{ route('gestion.supervision.films.index') }}" class="btn btn-primary btn-sm">Voir films</a>
                </div>
            </div>
        </div>

        <!-- Gestion Séances -->
        <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow">
            <div class="card-body">
                <h2 class="card-title text-success">
                    🎭 Séances
                </h2>
                <p class="text-base-content/70">Programmer et consulter les séances</p>
                <div class="card-actions justify-end">
                    <a href="{{ route('gestion.supervision.seances.index') }}" class="btn btn-success btn-sm">Voir séances</a>
                </div>
            </div>
        </div>

        <!-- Réservations -->
        <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow">
            <div class="card-body">
                <h2 class="card-title text-warning">
                    🎫 Réservations
                </h2>
                <p class="text-base-content/70">Consulter les réservations clients</p>
                <div class="card-actions justify-end">
                    <a href="{{ route('gestion.supervision.reservations.index') }}" class="btn btn-warning btn-sm">Voir réservations</a>
                </div>
            </div>
        </div>

        <!-- Gestion Billets -->
        <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow">
            <div class="card-body">
                <h2 class="card-title text-accent">
                    🎟️ Billets
                </h2>
                <p class="text-base-content/70">Consulter et gérer les billets émis</p>
                <div class="card-actions justify-end">
                    <a href="{{ route('gestion.supervision.billets.index') }}" class="btn btn-accent btn-sm">Voir billets</a>
                </div>
            </div>
        </div>

        <!-- Gestion Cinémas -->
        <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow">
            <div class="card-body">
                <h2 class="card-title text-info">
                    🏢 Cinémas
                </h2>
                <p class="text-base-content/70">Gérer les cinémas et salles</p>
                <div class="card-actions justify-end">
                    <a href="{{ route('gestion.supervision.cinemas.index') }}" class="btn btn-info btn-sm">Voir cinémas</a>
                </div>
            </div>
        </div>

        <!-- Gestion Clients -->
        <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow">
            <div class="card-body">
                <h2 class="card-title text-secondary">
                    👥 Clients
                </h2>
                <p class="text-base-content/70">Consulter la base clients</p>
                <div class="card-actions justify-end">
                    <a href="{{ route('gestion.supervision.clients.index') }}" class="btn btn-secondary btn-sm">Voir clients</a>
                </div>
            </div>
        </div>

        <!-- Incidents (Employés) -->
        <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow">
            <div class="card-body">
                <h2 class="card-title text-error">
                    ⚠️ Incidents
                </h2>
                <p class="text-base-content/70">Signaler un incident technique</p>
                <div class="card-actions justify-end">
                    <a href="{{ route('gestion.supervision.incidents.index') }}" class="btn btn-error btn-sm">Voir incidents</a>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>