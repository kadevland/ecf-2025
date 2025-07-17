@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'help' => null,
    'options' => [], // Pour select
    'rows' => 3, // Pour textarea
    'accept' => null, // Pour file
    'multiple' => false, // Pour select multiple
    'disabled' => false
])

<div class="form-field">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($type === 'select')
            <select 
                id="{{ $name }}" 
                name="{{ $name }}" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error($name) border-red-500 @enderror"
                @if($required) required @endif
                @if($multiple) multiple @endif
                @if($disabled) disabled @endif>
                
                @if(!$multiple && !$required)
                    <option value="">Sélectionner...</option>
                @endif
                
                @foreach($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @if(old($name, $value) == $optionValue) selected @endif>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        @elseif($type === 'textarea')
            <textarea 
                id="{{ $name }}" 
                name="{{ $name }}" 
                rows="{{ $rows }}"
                placeholder="{{ $placeholder }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error($name) border-red-500 @enderror"
                @if($required) required @endif
                @if($disabled) disabled @endif>{{ old($name, $value) }}</textarea>
        @elseif($type === 'file')
            <input 
                type="file" 
                id="{{ $name }}" 
                name="{{ $name }}"
                @if($accept) accept="{{ $accept }}" @endif
                @if($multiple) multiple @endif
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error($name) border-red-500 @enderror"
                @if($required) required @endif
                @if($disabled) disabled @endif>
        @elseif($type === 'checkbox')
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="{{ $name }}" 
                    name="{{ $name }}"
                    value="1"
                    @if(old($name, $value)) checked @endif
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded @error($name) border-red-500 @enderror"
                    @if($disabled) disabled @endif>
                @if($label)
                    <label for="{{ $name }}" class="ml-2 block text-sm text-gray-900">
                        {{ $label }}
                    </label>
                @endif
            </div>
        @elseif($type === 'radio')
            <div class="space-y-2">
                @foreach($options as $optionValue => $optionLabel)
                    <div class="flex items-center">
                        <input 
                            type="radio" 
                            id="{{ $name }}_{{ $optionValue }}" 
                            name="{{ $name }}"
                            value="{{ $optionValue }}"
                            @if(old($name, $value) == $optionValue) checked @endif
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 @error($name) border-red-500 @enderror"
                            @if($disabled) disabled @endif>
                        <label for="{{ $name }}_{{ $optionValue }}" class="ml-2 block text-sm text-gray-900">
                            {{ $optionLabel }}
                        </label>
                    </div>
                @endforeach
            </div>
        @else
            <input 
                type="{{ $type }}" 
                id="{{ $name }}" 
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error($name) border-red-500 @enderror"
                @if($required) required @endif
                @if($disabled) disabled @endif>
        @endif
    </div>

    @if($help)
        <p class="mt-2 text-sm text-gray-500">{{ $help }}</p>
    @endif

    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>