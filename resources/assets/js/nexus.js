function toggleChevron(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
}
/* export functions we went to be global */
window.toggleChevron = toggleChevron;

/* event listeners */

/* spoiler tag show/hide */
$("span.spoiler").click(function() {
    $(this).toggleClass('spoiler');
});
