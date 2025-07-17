<div class="navbar bg-base-100 shadow-sm">
    <div class="flex-1">
        <a href="#" class="btn btn-ghost text-xl text-primary font-bold">
            <x-icons.home-site />
        </a>
         <label class="btn btn-square btn-ghost" for="sidebar-drawer" aria-label="open sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                class="inline-block h-5 w-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </label>
    </div>
    <div class="flex-none flex items-center">
        {{-- Quick links --}}
        @if (count($navLinks) > 0)
            <div class="hidden lg:flex gap-2 mr-4">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}" class="btn btn-ghost btn-sm flex items-center">
                        {!! $link['icon'] ?? '' !!}
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
        {{-- Auth menu --}}
        <x-admin.auth.menu />
    </div>
</div>
