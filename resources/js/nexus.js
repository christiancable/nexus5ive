// js for bootstrap popups etc
import 'bootstrap';

/* event listeners */

// spoiler tag show/hide
document.querySelectorAll("span.spoiler").forEach(spoiler => {
  spoiler.addEventListener("click", function() {
      this.classList.toggle("spoiler");
  });
});

// disclosure toggle
document.querySelectorAll(".disclose").forEach(disclosure => {
  disclosure.addEventListener("click", function(e) {
      const heading = e.target.querySelector("span.oi");
      if (heading) {
          heading.classList.toggle("oi-chevron-right");
          heading.classList.toggle("oi-chevron-bottom");
      }
  });
});

// toggle cog-menu
document.getElementById("cog-menu-toggle").addEventListener("click", function() {
  document.querySelectorAll(".cog-menu").forEach(menu => {
    menu.classList.toggle("d-none");
  });
});