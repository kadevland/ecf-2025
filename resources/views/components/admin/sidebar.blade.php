{{--
@var array<array{title: string, links: array<array{href: string, label: string, icon: string}>}> $sidebarSections
@var string $sideBarTitle
--}}
<div class="w-64 min-h-screen bg-base-content">
    <div class="flex items-center p-4">
        <span class="text-xl font-bold text-primary flex-1">
            {{ $sideBarTitle }}
        </span>

        <label for="sidebar-drawer" class="btn btn-square btn-sm btn-ghost text-base-200">
            <x-lucide-panel-left-close class="w-6 h-6 mr-2" />
        </label>
    </div>

    <ul class="menu space-y-2">
        @foreach ($sidebarSections as $section)
            <li>
                <details open>
                    <summary class="text-secondary">{{ $section['title'] }}</summary>

                    <ul class="m-0">
                        @foreach ($section['links'] as $link)
                            <li>
                                <a href="{{ $link['href'] }}" class="flex items-center text-base-300">
                                    {!! $link['icon'] !!}
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            </li>
        @endforeach
    </ul>
</div>
