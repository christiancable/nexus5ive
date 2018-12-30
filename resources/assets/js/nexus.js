/* functions */
function refreshNotifications() {
  var notificationsURL = "/api/notificationsCount";
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


/* event listeners */

// spoiler tag show/hide
$("span.spoiler").click(function() {
  $(this).toggleClass("spoiler");
});

// disclosure toggle
$(".disclose").click(function(e) {
  let heading = $(e.target).find("span.oi");
  if (heading) {
    heading.toggleClass("oi-chevron-right oi-chevron-bottom");
  }
});


/* export functions we went to be global */
window.refreshNotifications = refreshNotifications;
window.pollForNotifications = pollForNotifications;

/* document ready */

$(document).ready(function() {
  // notificationPoll is only defined for auth'd users
  if (typeof window.notificationPoll === 'undefined') {
    // we do not know how often to poll - assume not logged in
  } else {
    window.pollForNotifications(window.notificationPoll);
  }
});