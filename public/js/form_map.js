// On sélectionne l'élément avec l'id "mobileToggleFilters"
const toggleButton = document.getElementById('mobileToggleFilters');

// Ajout d'un écouteur d'événements pour réagir au clic sur l'élément
toggleButton.addEventListener('click', function() {
  // On sélectionne l'élément avec l'id "map-options-mobile"
  const mapOptions = document.getElementById('map-options-mobile');
  
  // On bascule la classe "active" sur cet élément
  mapOptions.classList.toggle('active');
});
