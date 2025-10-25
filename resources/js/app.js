import './bootstrap';
import { createIcons, icons } from 'lucide';

// Initialize Lucide icons and expose to window for global access
window.lucide = { createIcons, icons };

// Auto-initialize icons on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    createIcons({ icons });
});
