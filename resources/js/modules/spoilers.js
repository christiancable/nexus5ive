const SPOILER_SELECTOR = 'span.spoiler';

export const handleSpoilers = () => {
    document.addEventListener('click', (e) => {
        const target = e.target.closest(SPOILER_SELECTOR);
        if (target) {
            target.classList.toggle('spoiler');
        }
    });
};
