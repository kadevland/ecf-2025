export function toastManager() {
    return {
        messages: [],
        duration: 3000,
        
        init() {
            // Écouter les événements de toast
            window.addEventListener('toast-show', (event) => {
                this.show(event.detail);
            });
        },
        
        show(message) {
            // Ajouter le message avec un ID unique
            const id = Date.now();
            this.messages.push({
                id,
                ...message
            });
            
            // Retirer automatiquement après la durée définie
            setTimeout(() => {
                this.remove(id);
            }, message.duration || this.duration);
        },
        
        remove(id) {
            this.messages = this.messages.filter(m => m.id !== id);
        }
    };
}

// Helper pour déclencher un toast depuis n'importe où
window.toast = {
    success(text, duration) {
        window.dispatchEvent(new CustomEvent('toast-show', {
            detail: { type: 'success', text, duration }
        }));
    },
    
    error(text, duration) {
        window.dispatchEvent(new CustomEvent('toast-show', {
            detail: { type: 'error', text, duration }
        }));
    },
    
    warning(text, duration) {
        window.dispatchEvent(new CustomEvent('toast-show', {
            detail: { type: 'warning', text, duration }
        }));
    },
    
    info(text, duration) {
        window.dispatchEvent(new CustomEvent('toast-show', {
            detail: { type: 'info', text, duration }
        }));
    }
};