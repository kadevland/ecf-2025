<x-app.layout title="Mot de passe oublié">
    <x-app.dark-section>
        <div class="flex-1 flex flex-col justify-center items-center">
            <a href="{{ route('accueil') }}" class="text-4xl font-bold text-primary">
                Cinéphoria
            </a>
            <h1 class="text-2xl text-secondary-content mt-6">Réinitialisation du mot de passe</h1>
            <p class="text-sm mt-4 text-secondary-content max-w-md text-center">
                Indiquez-nous votre adresse e-mail et nous vous enverrons un lien de réinitialisation de mot de passe.
            </p>

            <!-- Session Alert -->
            <div class="w-full max-w-lg mt-4">
                <x-ui.session-alert />
            </div>

            <div class="card bg-base-100 w-full max-w-lg shrink-0 shadow-2xl mt-5">
                <div class="card-body">
                    <form method="POST" action="{{ route('mot-de-passe-oublie.envoyer') }}" class="space-y-4">
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

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-full">
                            Envoyer le lien de réinitialisation
                        </button>

                        <!-- Back to Login -->
                        <div class="text-center">
                            <a href="{{ route('connexion') }}" class="link link-primary text-sm">
                                Retour à la connexion
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-app.dark-section>
</x-app.layout>