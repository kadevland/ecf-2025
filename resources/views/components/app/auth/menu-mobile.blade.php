@if($isAuthenticated)
    @foreach($userMenuLinks as $link)
        <li>
            @if(($link['action'] ?? '') === 'logout')
                <form method="POST" action="{{ $link['href'] }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full text-left">
                        {!! $link['icon'] ?? '' !!}
                        {{ $link['label'] }}
                    </button>
                </form>
            @else
                <a href="{{ $link['href'] }}" class="flex items-center">
                    {!! $link['icon'] ?? '' !!}
                    {{ $link['label'] }}
                </a>
            @endif
        </li>
    @endforeach
@else
    <li>
        <a href="{{ $urlLogin }}" class="flex items-center">
            <x-lucide-log-in class="w-4 h-4 mr-2" />
            Se connecter
        </a>
    </li>
    <li>
        <a href="{{ $urlRegister }}" class="flex items-center">
            <x-lucide-user-plus class="w-4 h-4 mr-2" />
            S'inscrire
        </a>
    </li>
@endif
