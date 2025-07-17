@props(['status' => null])

<x-app.dark-section>
    <div class="flex-1 flex flex-col justify-center items-center">
        <a href="{{ route('accueil') }}" class="text-4xl font-bold text-primary">
            Cinéphoria
        </a>
        <h1 class="text-2xl text-secondary-content mt-6">Mot de passe oublié</h1>
        <p class="text-sm mt-4 text-secondary-content text-center max-w-md">
            Saisissez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
        </p>
        
        <!-- Session Status -->
        @if($status)
            <div class="alert alert-success w-full max-w-sm mt-4">
                <span>{{ $status }}</span>
            </div>
        @endif

        <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl mt-5">
            <div class="card-body">
                <p class="flex justify-center text-accent-content text-lg font-semibold">Récupération</p>
                
                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf
                    
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
                                   autofocus />
                        </label>
                        @error('email')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full">
                        Envoyer le lien de réinitialisation
                    </button>
                    
                    <!-- Back to Login -->
                    <div class="text-center">
                        <a href="{{ route('connexion') }}" class="link link-primary text-sm font-medium">
                            Retour à la connexion
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app.dark-section>