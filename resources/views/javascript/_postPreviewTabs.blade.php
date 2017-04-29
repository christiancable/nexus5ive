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
</script>