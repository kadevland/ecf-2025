<footer class="footer footer-center p-10 bg-base-200 text-base-content rounded">
    <nav class="grid grid-flow-col gap-4">
        @foreach ($footerLinks as $link)
            <a href="{{ $link['href'] }}" class="link link-hover">{{ $link['label'] }}</a>
        @endforeach
    </nav>

    <nav>
        <div class="grid grid-flow-col gap-4">
            @foreach ($socialLinks as $social)
                <a href="{{ $social['href'] }}" class="link text-2xl" title="{{ $social['label'] }}">
                    {!! $social['icon'] !!}
                </a>
            @endforeach
        </div>
    </nav>

    <aside>
        <p class="text-primary font-semibold">{{ $companyName }}</p>
        <p>{{ $companyDescription }}</p>
    </aside>
</footer>
<div class="bg-base-300 py-4 px-6">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-base-content/70">
        <div class="flex flex-wrap gap-4">
            @foreach ($legalLinks as $link)
                <a href="{{ $link['href'] }}" class="link link-hover">{{ $link['label'] }}</a>
            @endforeach
        </div>
        <div class="text-center md:text-right">
            {{ $copyright }}
        </div>
    </div>
</div>
