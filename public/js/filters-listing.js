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
