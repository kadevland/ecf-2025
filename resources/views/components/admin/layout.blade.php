<!-- layout.blade.php -->
<!DOCTYPE html>
<html lang="fr" data-theme="admin-light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Administration' }} - Cinéphoria</title>

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Stack pour styles additionnels --}}
    @stack('styles')
</head>

<body class="bg-base-200">
    <div class="drawer">

        <input id="sidebar-drawer" type="checkbox" class="drawer-toggle" />
        <label for="sidebar-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="drawer-side">
            <label for="sidebar-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <x-admin.sidebar />
        </div>


        <div class="drawer-content">
            <div class ="min-h-screen flex">

                {{-- Sidebar --}}


                {{-- Main content area --}}
                <div class="flex-1 flex flex-col">
                    {{-- Top navbar --}}
                    <x-admin.navbar />

                    {{-- Breadcrumbs --}}
                    <x-admin.breadcrumbs :breadcrumbs="$breadcrumbs??[]"/>

                    {{-- Page header --}}
                    <x-admin.page-header />

                    {{-- Flash messages --}}
                    <x-admin.flash-messages />

                    {{-- Main content --}}
                    <main class="flex-1 p-6">
                        {{ $slot }}
                    </main>

                    {{-- Footer --}}
                    <x-admin.footer />
                </div>
            </div>

            {{-- Scripts additionnels --}}
            @stack('scripts')

            {{-- Modal container (pour les modals dynamiques) --}}
            <div id="modal-container"></div>


        </div>

</body>

</html>
