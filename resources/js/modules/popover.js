const SPOILER_SELECTOR = '[data-bs-toggle="popover"]';

export const handlePopovers = (bootstrap) => {
    document.querySelectorAll(SPOILER_SELECTOR).forEach(trigger => {
        new bootstrap.Popover(trigger)
    });
};