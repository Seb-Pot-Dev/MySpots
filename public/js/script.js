var formSpot = document.getElementById("form-spot-id");
var openBtn = document.getElementById("openBtn");
var closeBtn = document.getElementById("closeBtn");

openBtn.onclick = openFormSpot;
closeBtn.onclick = closeFormSpot;

function openFormSpot() {
  formSpot.classList.add("active");
  openBtn.style.display = "none"; // Masquer le bouton "+"
  closeBtn.style.display = "inline-block"; // Afficher le bouton "-"
}

function closeFormSpot() {
  formSpot.classList.remove("active");
  openBtn.style.display = "inline-block"; // Afficher le bouton "+"
  closeBtn.style.display = "none"; // Masquer le bouton "-"
}

var addSpotByClickOnMap = document.getElementById("addSpotByClickOnMap");

addSpotByClickOnMap.onclick = openFormSpot;

