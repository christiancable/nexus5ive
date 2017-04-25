<script type="text/javascript">
    function toggleChevron(e) {
        $(e.target)
            .prev('.panel-heading')
            .find("i.indicator")
            .toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
    }
    $('#newSectionAccordion').on('hidden.bs.collapse', toggleChevron);
    $('#newSectionAccordion').on('shown.bs.collapse', toggleChevron);
    $('#newTopicAccordion').on('hidden.bs.collapse', toggleChevron);
    $('#newTopicAccordion').on('shown.bs.collapse', toggleChevron);   
</script>