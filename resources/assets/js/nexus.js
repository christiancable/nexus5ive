function toggleChevron(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
}

function refreshNotifications() {

    console.log('checking for notifications');
    var notificationsURL = '/api/notificationsCount';
    var displayedNotificationsCount = $("#notification-count").text();

    $.get(notificationsURL, function(data) {
			updatedNotificationsCount = data;	
			if (displayedNotificationsCount != updatedNotificationsCount) {
                var toolbarURL = "/interface/toolbar";
                $("#top-toolbar").load(toolbarURL);
			}
    });
}

function pollForNotifications(time) {
    setInterval(window.refreshNotifications, time);        
}

/* export functions we went to be global */
window.toggleChevron = toggleChevron;
window.refreshNotifications = refreshNotifications;
window.pollForNotifications = pollForNotifications;

/* event listeners */

/* spoiler tag show/hide */
$("span.spoiler").click(function() {
    $(this).toggleClass('spoiler');
});

$( document ).ready(function() {
    window.pollForNotifications(window.notificationPoll)
});
