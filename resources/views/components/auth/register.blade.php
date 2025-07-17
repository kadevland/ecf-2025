<x-app.dark-section>
    <div class="flex-1 flex flex-col justify-center items-center">
        <a href="{{ route('accueil') }}" class="text-4xl font-bold text-primary">
            Cinéphoria
        </a>
        <h1 class="text-2xl text-secondary-content mt-6">Créer votre compte</h1>
        <p class="text-sm mt-4 text-secondary-content">Rejoignez la communauté Cinéphoria</p>

        <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl mt-5">
            <div class="card-body">
                <p class="flex justify-center text-accent-content text-lg font-semibold">S'inscrire</p>
                
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    
                    <!-- Name -->
                    <div>
                        <label class="label text-accent-content">
                            <span class="label-text">Nom complet</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2 @error('name') input-error @enderror">
                            <x-lucide-user class="w-4 h-4 opacity-70" />
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="grow" 
                                   placeholder="Votre nom complet" 
                                   required 
                                   autofocus 
                                   autocomplete="name" />
                        </label>
                        @error('name')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="label text-accent-content">
                            <span class="label-text">Adresse email</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2 @error('email') input-error @enderror">
                            <x-lucide-mail class="w-4 h-4 opacity-70" />
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   class="grow" 
                                   placeholder="votre@email.com" 
                                   required 
                                   autocomplete="username" />
                        </label>
                        @error('email')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="label text-accent-content">
                            <span class="label-text">Mot de passe</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2 @error('password') input-error @enderror">
                            <x-lucide-lock class="w-4 h-4 opacity-70" />
                            <input type="password" 
                                   name="password"
                                   class="grow" 
                                   placeholder="••••••••" 
                                   required 
                                   autocomplete="new-password" />
                        </label>
                        @error('password')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="label text-accent-content">
                            <span class="label-text">Confirmer le mot de passe</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2 @error('password_confirmation') input-error @enderror">
                            <x-lucide-lock class="w-4 h-4 opacity-70" />
                            <input type="password" 
                                   name="password_confirmation"
                                   class="grow" 
                                   placeholder="••••••••" 
                                   required 
                                   autocomplete="new-password" />
                        </label>
                        @error('password_confirmation')
                            <div class="label">
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