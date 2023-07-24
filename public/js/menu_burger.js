var sidenav = document.getElementById("mySidenav");
var openBtn = document.getElementById("nav-open-btn");
var closeBtn = document.getElementById("nav-close-btn");

openBtn.onclick = openNav;
closeBtn.onclick = closeNav;

/* Set the width of the side navigation to 250px */
function openNav() {
  sidenav.classList.add("active");
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  sidenav.classList.remove("active");
}

