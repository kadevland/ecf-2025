import Alpine from 'alpinejs'
import { toastManager } from './components/toast'

window.Alpine = Alpine

// Enregistrer les composants Alpine
Alpine.data('toastManager', toastManager)

Alpine.start()
