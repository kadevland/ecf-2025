@props(['actionList'])

@if($actionList->hasEnabledActions())
    @if($actionList->shouldUseDropdown())
        <x-admin.table.action.dropdown :actionList="$actionList" />
    @else
        {{-- Inline style --}}
        <div class="flex items-center space-x-1">
            @foreach($actionList->enabledActions() as $action)
                <x-admin.table.action.button :action="$action" />
            @endforeach
        </div>
    @endif
@endif