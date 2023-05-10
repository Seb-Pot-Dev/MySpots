

/*
 "L" est un namespace de Leaflet utilisé pour accéder aux fonctions et classes de la bibliothèque de Leaflet.

Par exemple, pour créer une carte, j'utilise la méthode L.map() de Leaflet.
De même, pour créer un marqueur, j'utilise la méthode L.marker(). 

*/

// Créer une carte Leaflet centrée sur les coordonnées du centre de Strasbourg [48.583328, 7.75] et avec un niveau de zoom de 14
var map = L.map('map').setView([48.583328, 7.75], 14);

// Ajouter une couche de tuiles OpenStreetMap à la carte, avec un maximum de zoom de 19 et une attribution
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);


//ESSAI tableau

//Créer un popup lorsque l'on clique sur un endroit non marked de la map et indique les coordonnées
    //stockage de la fonction popup() de Leaflet dans la variable popup
    var popup = L.popup();

function onMapClick(e) {
    popup
        .setLatLng(e.latlng)
        .setContent("You clicked the map at " + e.latlng.toString())
        .openOn(map);
}

map.on('click', onMapClick);


// Créer un tableau de coordonnées pour le polygone de l'agglomération de Strasbourg
//(revoir la précision)

var polygonCoords = [  [48.623860, 7.727798],
[48.604371, 7.693883],
[48.575899, 7.686080],
[48.568368, 7.729530],
[48.556536, 7.741879],
[48.540214, 7.740874],
[48.520945, 7.753266],
[48.517002, 7.802228],
[48.525297, 7.830220],
[48.548539, 7.848697],
[48.563863, 7.858974],
[48.586015, 7.840496],
[48.605270, 7.820451],
[48.610125, 7.795036],
[48.623860, 7.727798]
];
// Créer un polygone à partir des coordonnées stockées dans var polygonCoords et l'ajouter à la carte
var myPolygon = L.polygon(polygonCoords, {
    color: 'red',
    fillColor: 'transparent',
    weight: 2
}).addTo(map);


/* ---- AJOUT DE LIEUX  ------- */



  
