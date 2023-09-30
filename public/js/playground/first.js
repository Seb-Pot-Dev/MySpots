// Fonction pour ajouter l'attribut "autocompleted" à un élément
function addAutocompletedAttribute(element) {
    element.addAttribute('autocompleted', '');
    
    // Attendre 1 seconde avant de le supprimer
    setTimeout(function() {
        element.removeAttribute('autocompleted');
    }, 1000); // 1000 millisecondes équivalent à 1 seconde
}

// Sélectionner tous les éléments input avec la classe "manual"
var elements = document.querySelectorAll('input');

console.log(elements)
// Parcourir tous les éléments et ajouter/supprimer l'attribut "autocompleted"
elements.forEach(function(element) {
    // Ajouter l'attribut "autocompleted"
    addAutocompletedAttribute(element);
});

// Explique pour mon dossier : Ce code définit une fonction nommée "addAutocompletedAttribute" qui ajoute l'attribut "autocompleted" à un élément à l'aide de setAttribute, puis utilise setTimeout pour attendre 1 seconde avant de le supprimer avec removeAttribute. Ensuite, il sélectionne tous les éléments input avec la classe "manual", parcourt chaque élément, et ajoute puis supprime l'attribut "autocompleted".
