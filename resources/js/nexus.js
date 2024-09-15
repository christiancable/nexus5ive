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

//toggle cog-menu
$("#cog-menu-toggle").click(function(e) {
  $(".cog-menu").toggleClass("d-none");
}); 