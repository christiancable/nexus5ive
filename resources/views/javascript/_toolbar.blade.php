<script type="text/javascript">
	var refreshToolbar = function() {
		var toolbarURL = "{{route('interface.toolbar')}}";
		$("#top-toolbar").load(toolbarURL);
	}

	var checkNotifications = function() {
		var notificationURL = "{{route('api.notificationCount')}}";
		var shownNotificaions = $("#notification-count").text();
		var currentNotifications = shownNotificaions;

		$.get(notificationURL, function(data) {
			currentNotifications = data;	
			if (currentNotifications != shownNotificaions) {
				refreshToolbar();
			}
		});
	}

	$(document).ready(function() {
		setInterval(checkNotifications, 2000)
	});
</script>