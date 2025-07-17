<!DOCTYPE html>
<html lang="fr" data-theme="cinephoria">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Cinéphoria' }} - Réseau de cinémas</title>
    <meta name="description"
        content="{{ $description ?? 'Découvrez Cinéphoria, votre réseau de cinémas en France et Belgique. Réservez vos billets en ligne pour les derniers films.' }}">

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- Open Graph / Social Media --}}
    <meta property="og:title" content="{{ $title ?? 'Cinéphoria' }}">
    <meta property="og:description" content="{{ $description ?? 'Votre réseau de cinémas responsable' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Stack pour styles additionnels --}}
    @stack('styles')
</head>

<body class="bg-base-100">
    <div class="min-h-screen flex flex-col">
        {{-- Navigation --}}
        <x-app.navbar />
        {{-- Breadcrumbs --}}
        <x-app.breadcrumbs />
        {{-- Page Hero/Header --}}
        <x-app.hero-header />
        {{-- Flash messages --}}
        <x-app.flash-messages />
        {{-- Main content --}}
        <main class="flex-1 flex flex-col">
            {{ $slot }}
        </main>
        {{-- Footer --}}
        <x-app.footer />
    </div>

    {{-- Scripts additionnels --}}
    @stack('scripts')
    {{-- Modal container --}}
    <div id="modal-container"></div>

    {{-- Back to top button --}}
    <button id="backToTop"
        class="btn btn-circle btn-primary fixed bottom-4 right-4 opacity-0 transition-opacity duration-300 z-50">
        <x-lucide-arrow-up class="w-5 h-5" />
    </button>

    <script>
        // Back to top functionality
        const backToTopButton = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0');
                backToTopButton.classList.add('opacity-100');
            } else {
                backToTopButton.classList.add('opacity-0');
                backToTopButton.classList.remove('opacity-100');
            }
        });

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>

</html>
