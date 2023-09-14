const toggleFilters = document.getElementById('toggleFilters')

toggleFilters.addEventListener('click', function() {
    const container = document.getElementById('search-form');
    const arrows = document.querySelectorAll('.arrow-toggle-filters');

    if (container.style.maxHeight !== '0px') {
        container.style.maxHeight = '0px';
        container.style.overflow = 'hidden';
        toggleFilters.style.top = '92px' 
        arrows.forEach(function(arrow) {
            arrow.innerHTML = 'OUVRIR FILTRES';
            // Do something with each arrow element
        });
        
    } else {
        toggleFilters.style.top = '474px' 
        container.style.maxHeight = '100%'; // Rétablissez ceci à votre max-width initial*
        arrows.forEach(function(arrow) {
            // Do something with each arrow element
            arrow.innerHTML = 'FERMER FILTRES';
        });
        
    }
});

/**************** MEME LOGIQUE POUR LA LISTE DES SPOTS *********************** */
const toggleList = document.getElementById('toggleListSpots')
const containerList = document.getElementById('list-spots-desktop-id');

toggleList.addEventListener('click', function() {
    if (containerList.style.maxHeight !== '0px') {
        containerList.style.maxHeight = '0px';
        containerList.style.overflow = 'hidden';
        toggleList.style.top = '92px' 
        toggleList.innerHTML = 'OUVRIR LISTE';
    } else {
        toggleList.style.top = '474px' 
        containerList.style.maxHeight = '350px'; // Rétablissez ceci à votre max-width initial*
        toggleList.innerHTML = 'FERMER LISTE';
    }
});
/**************** MEME LOGIQUE POUR LE FORMULAIRE D'AJOUT *********************** */
const toggleForm = document.getElementById('toggleFormSpot');
const containerForm = document.getElementById('form-spot-id');

toggleForm.addEventListener('click', function(event) {

    // pour voir l'état actuel des styles de form-spot-id  (pas uniquement ceux appliqués en ligne sur l'element HTML, mais aussi ceux issus de feuilles css)
    let computedStyle = window.getComputedStyle(containerForm);
    
    if (computedStyle.maxHeight !== '0px') {
        containerForm.style.maxHeight = '0px';
        containerForm.style.overflow = 'hidden';
        toggleForm.innerHTML = 'OUVRIR';
    } else {
        containerForm.style.maxHeight = 'unset';
        containerForm.style.overflow = 'unset';
        toggleForm.innerHTML = 'FERMER';
    }
});
