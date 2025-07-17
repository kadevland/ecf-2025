@props(['status' => null])

<x-app.dark-section>
    <div class="flex-1 flex flex-col justify-center items-center">
        <a href="{{ route('accueil') }}" class="text-4xl font-bold text-primary">
            Cinéphoria
        </a>
        <h1 class="text-2xl text-secondary-content mt-6">Connexion à votre compte</h1>
        <p class="text-sm mt-4 text-secondary-content">Accédez à votre compte pour réserver vos places</p>

        <!-- Session Alert -->
        <div class="w-full max-w-lg mt-4">
            <x-ui.session-alert />
        </div>

        <div class="card bg-base-100 w-full max-w-lg shrink-0 shadow-2xl mt-5">
            <div class="card-body">
                <p class="flex justify-center text-accent-content text-lg font-semibold">Se connecter</p>

                <form method="POST" action="{{ route('connexion.store') }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="label text-accent-content">
                            <span class="label-text">Adresse email</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="input input-bordered w-full @error('email') input-error @enderror"
                            placeholder="votre@email.com" required autofocus autocomplete="username" />
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
                        <input type="password" name="password"
                            class="input input-bordered w-full @error('password') input-error @enderror"
                            placeholder="••••••••" required autocomplete="current-password" />
                        @error('password')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex justify-between items-center">
                        <label class="label cursor-pointer">
                            <input type="checkbox" name="remember" class="checkbox checkbox-xs" />
                            <span class="label-text ml-2">Se souvenir de moi</span>
                        </label>

                        <a class="link link-hover text-primary text-sm font-medium" href="{{ route('mot-de-passe-oublie') }}">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full">
                        Se connecter
                    </button>

                    <!-- Register Link -->

                    <div class="text-center">
                        <span class="text-sm text-base-content/70">Pas encore de compte ?</span>
                        <a href="{{ route('creer-compte') }}" class="link link-primary text-sm font-medium ml-1">
                            Créer un compte
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app.dark-section>
