<x-app.layout title="Contact">
    <div class="min-h-screen bg-base-200">
        <!-- Hero Section -->
        <div class="hero bg-primary text-primary-content">
            <div class="hero-content text-center py-16">
                <div class="max-w-md">
                    <h1 class="text-5xl font-bold">Contactez-nous</h1>
                    <p class="py-6">Une question ? Une suggestion ? N'hésitez pas à nous contacter</p>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Formulaire de contact -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title text-2xl mb-4">Envoyez-nous un message</h2>
                            
                            <!-- Alert de succès -->
                            <x-ui.session-alert />
                            
                            <form action="{{ route('contact.envoyer') }}" method="POST" class="space-y-4">
                                @csrf
                                
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Votre nom</span>
                                    </label>
                                    <input type="text" name="nom" value="{{ old('nom') }}" 
                                           class="input input-bordered @error('nom') input-error @enderror" required>
                                    @error('nom')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Votre email</span>
                                    </label>
                                    <input type="email" name="email" value="{{ old('email') }}" 
                                           class="input input-bordered @error('email') input-error @enderror" required>
                                    @error('email')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Cinéma concerné (optionnel)</span>
                                    </label>
                                    <select name="cinema" class="select select-bordered">
                                        <option value="">-- Sélectionnez un cinéma --</option>
                                        <option value="paris">Cinéphoria Paris</option>
                                        <option value="lyon">Cinéphoria Lyon</option>
                                        <option value="marseille">Cinéphoria Marseille</option>
                                        <option value="lille">Cinéphoria Lille</option>
                                        <option value="nantes">Cinéphoria Nantes</option>
                                        <option value="bruxelles">Cinéphoria Bruxelles</option>
                                        <option value="liege">Cinéphoria Liège</option>
                                    </select>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Sujet</span>
                                    </label>
                                    <input type="text" name="sujet" value="{{ old('sujet') }}" 
                                           class="input input-bordered @error('sujet') input-error @enderror" required>
                                    @error('sujet')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Votre message</span>
                                    </label>
                                    <textarea name="message" rows="5" 
                                              class="textarea textarea-bordered @error('message') textarea-error @enderror" 
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control mt-6">
                                    <button type="submit" class="btn btn-primary">Envoyer le message</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Informations de contact -->
                    <div class="space-y-6">
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h2 class="card-title text-2xl mb-4">Siège social</h2>
                                <div class="space-y-3">
                                    <p class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>
                                            Cinéphoria Group<br>
                                            15 Boulevard des Capucines<br>
                                            75009 Paris, France
                                        </span>
                                    </p>
                                    
                                    <p class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <span>01 42 65 89 00</span>
                                    </p>
                                    
                                    <p class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>contact@cinephoria.fr</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h2 class="card-title text-2xl mb-4">Service client</h2>
                                <div class="space-y-3">
                                    <div class="alert alert-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Disponible du lundi au samedi de 9h à 20h</span>
                                    </div>
                                    
                                    <p>Pour toute question relative à :</p>
                                    <ul class="list-disc list-inside space-y-1 ml-4">
                                        <li>Réservations et billetterie</li>
                                        <li>Programme et horaires</li>
                                        <li>Cartes d'abonnement</li>
                                        <li>Événements spéciaux</li>
                                        <li>Objets trouvés</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h2 class="card-title text-2xl mb-4">Suivez-nous</h2>
                                <div class="flex gap-4">
                                    <button class="btn btn-circle btn-outline">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </button>
                                    <button class="btn btn-circle btn-outline">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                    </button>
                                    <button class="btn btn-circle btn-outline">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                                            <path d="M12 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a3.999 3.999 0 110-7.998 3.999 3.999 0 010 7.998z"/>
                                            <circle cx="18.406" cy="5.594" r="1.44"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app.layout>