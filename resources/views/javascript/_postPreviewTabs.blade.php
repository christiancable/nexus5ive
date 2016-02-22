<script type="text/javascript">
jQuery(document).ready(function ($) {
    @foreach ($tabGroups as $tabGroup)
        $('#{{$tabGroup}} a').click(function (e) {
            e.preventDefault();
        	if ($(this).attr("href") == "#preview") {
        		postPreview($(this));
        	}
        	$(this).tab('show');        		
        	
        })
    @endforeach   
});

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
</script>