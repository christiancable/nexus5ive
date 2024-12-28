const SELECTORS = {
    COG_MENU: '.cog-menu',
    COG_TOGGLE: '#cog-menu-toggle'
};

export const handleCogMenu = () => {
    const cogToggle = document.querySelector(SELECTORS.COG_TOGGLE);
    if (!cogToggle) {
        return;
    }

    cogToggle.addEventListener('click', () => {
        document.querySelectorAll(SELECTORS.COG_MENU).forEach(menu => {
            menu.classList.toggle('d-none');
        });
    });
};
