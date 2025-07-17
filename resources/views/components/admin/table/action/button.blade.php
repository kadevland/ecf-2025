@props(['action'])

@if($action->isClickable())
    <a href="{{ $action->url }}" 
       class="{{ $action->buttonClass() }}"
       @foreach($action->attributes() as $attr => $value)
           {{ $attr }}="{{ $value }}"
       @endforeach>
        @if($action->hasIcon())
            <svg class="w-4 h-4 @if($action->label) mr-1 @endif" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="{{ $action->icon }}">
                </path>
            </svg>
        @endif
        @if($action->label)
            <span>{{ $action->label }}</span>
        @endif
    </a>
@else
    <button class="{{ $action->buttonClass() }}" disabled>
        @if($action->hasIcon())
            <svg class="w-4 h-4 @if($action->label) mr-1 @endif" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="{{ $action->icon }}">
                </path>
            </svg>
        @endif
        @if($action->label)
            <span>{{ $action->label }}</span>
        @endif
    </button>
@endif