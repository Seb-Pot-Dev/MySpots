// Gestion de l'ouverture/fermeture du formulaire d'ajout de spots

var formSpot = document.getElementById("form-spot-id");
var openBtnFormSpot = document.getElementById("open-formSpot-btn");
var closeBtnFormSpot = document.getElementById("close-formSpot-btn");

openBtnFormSpot.onclick = openFormSpot;
closeBtnFormSpot.onclick = closeFormSpot;

function openFormSpot() {
  formSpot.classList.add("active");
  openBtnFormSpot.style.display = "none"; // Masquer le bouton "+"
  closeBtnFormSpot.style.display = "flex"; // Afficher le bouton "-"
}

function closeFormSpot() {
  formSpot.classList.remove("active");
  openBtnFormSpot.style.display = "block"; // Afficher le bouton "+"
  closeBtnFormSpot.style.display = "none"; // Masquer le bouton "-"
}

var addSpotByClickOnMap = document.getElementById("addSpotByClickOnMap");

addSpotByClickOnMap.onclick = openFormSpot;

