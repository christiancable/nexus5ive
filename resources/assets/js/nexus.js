function toggleChevron(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
}


function postPreview(tab) {
    $.ajax({
        type: 'POST',
        url: '/api/nxcode',
        data: {
            'text': $('#postText').val(),
            '_token': $('input[name=_token]').val()
        },
        dataType: 'JSON',
        success: function (data) {
            if ($('input[name=title]').val()) {
                $('#preview-title').html($('input[name=title]').val());
            }
            $('#preview-view').html(data.text);
        }
    })
}

/* export functions we went to be global */
window.toggleChevron = toggleChevron;
window.postPreview = postPreview;

/* event listeners */

/* spoiler tag show/hide */
$("span.spoiler").click(function() {
    $(this).toggleClass('spoiler');
});
