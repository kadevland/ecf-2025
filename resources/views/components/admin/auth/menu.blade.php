@if ($isAuthenticated)
    <div class="dropdown dropdown-end">
        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
            <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                {{ $userInitial }}
            </div>
        </div>
        <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
            <li class="menu-title">
                <span>{{ $userName }}</span>
            </li>
            <li>
                <hr class="my-1">
            </li>

            @foreach ($userMenuLinks as $link)
                <li>
                    @if (($link['action'] ?? '') === 'logout')
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
        </ul>
    </div>
@endif
