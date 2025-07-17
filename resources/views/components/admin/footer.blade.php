<footer class="footer p-4 bg-base-300 text-base-content border-t">
    <div class="flex justify-between items-center w-full">
        <div class="flex gap-4">
            @foreach($adminLinks as $link)
                <a href="{{ $link['href'] }}" class="link link-hover text-sm">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="text-sm text-base-content/70">
            {{ $adminVersion }} • {{ $lastUpdate }}
        </div>
    </div>
</footer>
