<x-app.layout title="Créer un compte">
    <x-app.dark-section>
        <div class="flex-1 flex flex-col justify-center items-center py-8">
            <a href="{{ route('accueil') }}" class="text-4xl font-bold text-primary">
                Cinéphoria
            </a>
            <h1 class="text-2xl text-secondary-content mt-4">Créer votre compte</h1>
            <p class="text-sm mt-2 text-secondary-content">Rejoignez Cinéphoria pour réserver vos séances</p>

            <!-- Session Alert -->
            <div class="w-full max-w-lg mt-3">
                <x-ui.session-alert />
            </div>

            <div class="card bg-base-100 w-full max-w-lg shrink-0 shadow-2xl mt-3">
                <div class="card-body">
                    <!-- Info Site Demo -->
                    <div class="alert alert-warning mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-xs">Site de démonstration - Les comptes ne sont pas réellement créés</span>
                    </div>

                    <form method="POST" action="{{ route('creer-compte.store') }}" class="space-y-3">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label class="label text-accent-content">
                                <span class="label-text">Adresse email</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="input input-bordered w-full @error('email') input-error @enderror"
                                placeholder="votre@email.com" required autofocus />
                            @error('email')
                                <div class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Prénom et Nom -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label text-accent-content">
                                    <span class="label-text">Prénom</span>
                                </label>
                                <input type="text" name="prenom" value="{{ old('prenom') }}"
                                    class="input input-bordered w-full @error('prenom') input-error @enderror"
                                    placeholder="Jean" required />
                                @error('prenom')
                                    <div class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <div>
                                <label class="label text-accent-content">
                                    <span class="label-text">Nom</span>
                                </label>
                                <input type="text" name="nom" value="{{ old('nom') }}"
                                    class="input input-bordered w-full @error('nom') input-error @enderror"
                                    placeholder="Dupont" required />
                                @error('nom')
                                    <div class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password et Confirmation -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label text-accent-content">
                                    <span class="label-text">Mot de passe</span>
                                </label>
                                <input type="password" name="password"
                                    class="input input-bordered w-full @error('password') input-error @enderror"
                                    placeholder="••••••••" required />
                                @error('password')
                                    <div class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <div>
                                <label class="label text-accent-content">
                                    <span class="label-text">Confirmer</span>
                                </label>
                                <input type="password" name="password_confirmation"
                                    class="input input-bordered w-full"
                                    placeholder="••••••••" required />
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="terms" class="checkbox checkbox-xs" required />
                                <span class="label-text text-xs leading-relaxed">
                                    J'accepte les <a href="#" class="link link-primary">conditions d'utilisation</a> 
                                    et la <a href="#" class="link link-primary">politique de confidentialité</a>
                                </span>
                            </label>
                            @error('terms')
                                <div class="label pt-0">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-full">
                            Créer mon compte
                        </button>

                        <!-- Login Link -->
                        <div class="text-center">
                            <span class="text-sm text-base-content/70">Déjà un compte ?</span>
                            <a href="{{ route('connexion') }}" class="link link-primary text-sm font-medium ml-1">
                                Se connecter
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-app.dark-section>
</x-app.layout>