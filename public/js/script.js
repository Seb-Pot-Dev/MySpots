// Gestion de l'ouverture/fermeture du formulaire d'ajout de spots

// Déclarations des variables 
// toggle switch
var switchFormSpot = document.querySelector('.toggle-checkbox');
// click map
var addSpotByClickOnMap = document.getElementById("addSpotByClickOnMap");
// le formulaire 
var formSpot = document.getElementById("form-spot-id");

// Déclaration des fonctions
  // Ouvrir le formulaire
  function openFormSpot() {
    formSpot.classList.toggle("active");
  }
  // Fermer le formulaire
  function closeFormSpot() {
    formSpot.classList.remove("active");
  }

// Ecouteurs d'évenements
  //si click sur switchFormSpot, ouvrir le formulaire de spot
  switchFormSpot.addEventListener('click', openFormSpot);
  //si click sur addSpotByClickOnMap, ouvrir le formulaire de spot
  addSpotByClickOnMap.addEventListener('click', openFormSpot);




