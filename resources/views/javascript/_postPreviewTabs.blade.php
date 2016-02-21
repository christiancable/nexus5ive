<script type="text/javascript">
jQuery(document).ready(function ($) {
    @foreach ($tabGroups as $tabGroup)
        $('#{{$tabGroup}} a').click(function (e) {
            e.preventDefault();
        	if ($(this).attr("href") == "#preview") {
        		postPreview();
        	}
        	$(this).tab('show')
        	
        })
    @endforeach   
});

function postPreview() {
	$.ajax({
	            type: 'POST',
	            url: '/api/nxcode',
	            data: {
	                'text': $('#postText').val(),
	                '_token': $('input[name=_token]').val()
	            },
	            dataType: 'JSON',
	            success: function (data) {
	                $('#preview-view').html(data.text);
	                $('#preview-title').html($('input[name=title]').val());
	            }
	})
}
</script>