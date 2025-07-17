@if($message)
<div role="alert" class="alert {{ $classes }}" x-data="{ show: true }" x-show="show" x-transition>
    {!! $icon !!}
    <span>{{ $message }}</span>
    <button @click="show = false" class="btn btn-sm btn-ghost">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
@endif