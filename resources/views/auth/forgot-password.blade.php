<x-app.layout>
    <x-slot name="title">Mot de passe oublié</x-slot>
    <x-slot name="description">Réinitialisez votre mot de passe Cinéphoria</x-slot>

    <x-auth.forgot-password :status="session('status')" />
</x-app.layout>