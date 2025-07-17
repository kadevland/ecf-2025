<x-app.layout>
    <x-slot name="title">Connexion</x-slot>
    <x-slot name="description">Connectez-vous à votre compte Cinéphoria</x-slot>

    <x-auth.login :status="session('status')" />
</x-app.layout>