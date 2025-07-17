@props(['actionList'])

<div class="dropdown dropdown-end">
    <div tabindex="0" role="button" class="btn btn-sm btn-ghost">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="{{ $actionList->dropdownIcon }}">
            </path>
        </svg>
        <span class="sr-only">{{ $actionList->dropdownLabel }}</span>
    </div>
    <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 z-50">
        @foreach($actionList->enabledActions() as $action)
            <li>
                @if($action->isClickable())
                    <a href="{{ $action->url }}" 
                       @foreach($action->attributes() as $attr => $value)
                           {{ $attr }}="{{ $value }}"
                       @endforeach>
                        @if($action->hasIcon())
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" 
                                      stroke-linejoin="round" 
                                      stroke-width="2" 
                                      d="{{ $action->icon }}">
                                </path>
                            </svg>
                        @endif
                        {{ $action->label }}
                    </a>
                @else
                    <span class="text-gray-400">
                        @if($action->hasIcon())
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" 
                                      stroke-linejoin="round" 
                                      stroke-width="2" 
                                      d="{{ $action->icon }}">
                                </path>
                            </svg>
                        @endif
                        {{ $action->label }}
                    </span>
                @endif
            </li>
        @endforeach
    </ul>
</div>