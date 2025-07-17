<div class="toast {{ $position }}" 
     x-data="toastManager()"
     x-init="duration = {{ $duration }}">
    <template x-for="message in messages" :key="message.id">
        <div class="alert" 
             :class="{
                'alert-success': message.type === 'success',
                'alert-error': message.type === 'error',
                'alert-warning': message.type === 'warning',
                'alert-info': message.type === 'info'
             }"
             x-transition
             @click="remove(message.id)">
            <span x-text="message.text"></span>
        </div>
    </template>
</div>