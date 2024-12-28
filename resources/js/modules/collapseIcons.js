const COLLAPSE_TRIGGER = '[data-bs-toggle="collapse"]';

export const handleCollapseIcons = () => {
    document.querySelectorAll(COLLAPSE_TRIGGER).forEach(trigger => {
        const targetId = trigger.getAttribute('data-bs-target');
        const collapseElement = document.querySelector(targetId);
        
        if (collapseElement) {
            collapseElement.addEventListener('show.bs.collapse', () => {
                const icon = trigger.querySelector('.icon_mini');
                if (icon) icon.classList.add('rotate-90');
            });

            collapseElement.addEventListener('hide.bs.collapse', () => {
                const icon = trigger.querySelector('.icon_mini');
                if (icon) icon.classList.remove('rotate-90');
            });
        }
    });
};
