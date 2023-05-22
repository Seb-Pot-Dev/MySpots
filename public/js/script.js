var formSpot = document.getElementById("form-spot-id");
var openBtn = document.getElementById("openBtn");
var closeBtn = document.getElementById("closeBtn");

openBtn.onclick = openFormSpot;
closeBtn.onclick = closeFormSpot;

/* Set the width of the side navigation to 250px */
function openFormSpot() {
  formSpot.classList.add("active");
}

/* Set the width of the side navigation to 0 */
function closeFormSpot() {
  formSpot.classList.remove("active");
}