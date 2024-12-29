import './bootstrap';
import * as bootstrap from 'bootstrap';

import { handleSpoilers } from './modules/spoilers';
import { handleCollapseIcons } from './modules/collapseIcons';
import { handleCogMenu } from './modules/cogMenu';
import { handlePopovers } from './modules/popover';

// Initialize all handlers
const init = () => {
    try {
        handleSpoilers();
        handleCollapseIcons();
        handleCogMenu();
        handlePopovers(bootstrap);
    } catch (error) {
        console.error('Error initializing event handlers:', error);
    }
};

// Run initialization when DOM is ready
document.addEventListener('DOMContentLoaded', init);