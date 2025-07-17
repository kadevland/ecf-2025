@props([
    'title' => 'Formulaire',
    'action' => '',
    'method' => 'POST',
    'submitLabel' => 'Enregistrer',
    'cancelRoute' => null,
    'cancelLabel' => 'Annuler'
])

<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
    </div>

    <!-- Form -->
    <form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" x-data="formCard">
        @if($method !== 'GET' && $method !== 'POST')
            @method($method)
        @endif
        
        @if($method !== 'GET')
            @csrf
        @endif

        <!-- Form Content -->
        <div class="space-y-6">
            {{ $slot }}
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            @if($cancelRoute)
                <a href="{{ $cancelRoute }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    {{ $cancelLabel }}
                </a>
            @endif
            
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    :disabled="loading"
                    :class="{ 'opacity-50 cursor-not-allowed': loading }">
                <span x-show="!loading">{{ $submitLabel }}</span>
                <span x-show="loading" class="flex items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Enregistrement...
                </span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('formCard', () => ({
        loading: false,
        
        init() {
            this.$el.addEventListener('submit', () => {
                this.loading = true;
            });
        }
    }))
})
</script>