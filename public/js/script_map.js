// défini une variable marqueur quelconque pour le click sur la carte
let marqueur

// défini la manière dont la carte est centré et le zoom
let map = L.map('map').setView([48.583328, 7.75], 14);

    // Ajouter une couche de tuiles OpenStreetMap à la carte, avec un maximum de zoom de 19 et une attribution
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    
/*********************************GESTION DU CLICK SUR LA MAP****************************************************************/

//Appel la fonction onMapClick pour créer un Event lorsque l'on clique sur un endroit non marked de la map et indique les coordonnées
map.on('click', onMapClick);

//stockage de la fonction popup() de Leaflet dans la variable popup
var popup = L.popup();

// Lorsque je déselectionne un des champs 'spot_city, spot_cp etc..', utiliser la fonction getPosByAdress
/* getPosByAdress permet de définir de définir un marqueur grâce a l'API Nominatim, 
qui grâce a l'adresse permet de retrouver la latitude/longitude  */
document.querySelector('#spot_city').addEventListener('blur', getPosByAdress)
document.querySelector('#spot_cp').addEventListener('blur', getPosByAdress)
document.querySelector('#spot_adress').addEventListener('blur', getPosByAdress)


//Fonction pour créer un marqueur lorsque l'on clique sur un endroit de la map et renseigner les champs lat/lng du formulaire d'ajout de spot
function onMapClick(e) { 
    // on récupère les coordonnées du clic
    let pos = e.latlng

    //on ajoute un marqueur 
    addMarker(pos)

    // on affiche les coordonnées dans le formulaire
    document.querySelector('#spot_lat').value = pos.lat
    document.querySelector('#spot_lng').value = pos.lng

    //pour créer un pop up qui indique les lat/lng de l'endroit cliqué
    /*popup
        .setLatLng(e.latlng)
        .setContent("You clicked the map at " + e.latlng.toString())
        .openOn(map);
}

//fonction pour ajouter un marqueur sur la carte
function addMarker(pos){
    if(marqueur!=undefined){
        map.removeLayer(marqueur)
    }

    marqueur = L.marker(pos, {
        //On rend le marqueur déplaçable
        draggable: true
    })

    marqueur.addTo(map)

    //On écoute le drag&drop sur le marqueur de façon a pouvoir déplacer le marqueur et mettre aussi a jour les lat/lng
    marqueur.on("dragend", function(e){
        pos = e.target.getLatLng()

        document.querySelector('#spot_lat').value = pos.lat
        document.querySelector('#spot_lng').value = pos.lng
    })
}
//fonction pour récupérer la pos à partir de l'adresse entrée dans le formulaire de Spot (SpotType)
function getPosByAdress(){
    //On fabrique l'adresse
    let adresse = document.querySelector('#spot_adress').value + ", " + document.
    querySelector("#spot_cp").value + " " + document.querySelector('#spot_city').value

    // On initialise une requête AJAX
    const xmlhttp = new XMLHttpRequest

    xmlhttp.onreadystatechange = () => {
        // Si la requête est terminée 
        if(xmlhttp.readyState == 4){
            // Si on a une une réponse
            if(xmlhttp.status == 200){
                //on récupère la réponse
                let response = JSON.parse(xmlhttp.response)
                
                let lat = response[0]["lat"]
                let lng = response[0]["lon"]

                document.querySelector('#spot_lat').value = lat
                document.querySelector('#spot_lng').value = lng

                let pos = [lat, lng]
                addMarker(pos)

                map.setView(pos, 13)
            }
        }
    }

    // On ouvre la requête HTTP
    xmlhttp.open("get", `https://nominatim.openstreetmap.org/search?q=${adresse}&format=json&addressdetails=1&limit=1&polygon_svg=1`)
    
    xmlhttp.send()
}


/************************************FIN GESTION DU CLICK SUR LA CARTE********************************************/


/*******************AFFICHER DES POINTS ISSUS DE LA BASE DE DONNEES***********************************************/

    // Convertir un tableau TWIG en tableau JS
    const markersSpots = '{{ spots | raw }}';
    const jsonTabForMarkersSpots = JSON.parse(markersSpots);

    // Parcourir le tableau et créer des marqueurs pour chaque élément
    jsonTabForMarkersSpots.forEach(obj => {
        Object.entries(obj).forEach(([name, infos]) => { 
            const lat = infos[0]
            const lng = infos[1]
            const desc = infos[2]
            const isValidate = infos[3]
            const spotId = infos[4] 
            const avgNote = infos[5]

        // Si le spot a été validé par un admin, alors créé un marqueur
        if (isValidate){
            const marker = L.marker([lat, lng]).addTo(map);
            
            //pour créer l'URL de show_spot
            var urlShowSpot = '{{ path("show_spot", {'idSpot': 'exemple_id'}) }}';
            urlShowSpot = urlShowSpot.replace("exemple_id", spotId);

                //si la avgNote n'est pas null (c-a-d le spot a déjà été noté)
                if(avgNote){
                const popupContent = `<a href=${urlShowSpot}> <b>${name}, <i class='fa-solid fa-star'></i>(${avgNote})</b><br>${desc}</a>`;
                marker.bindPopup(popupContent);
                }else{ //Sinon on écrit 'pas de avgNote' a la place du int
                let avgNote = '(Pas de avgNote)'
                const popupContent = `<a href=${urlShowSpot}> <b>${name}, <i class='fa-regular fa-star'></i>${avgNote}</b><br>${desc}</a>`;
                marker.bindPopup(popupContent);
            }
        }
        })
    })
/************************************FIN AFFICHER DES POINTS ISSUS DE LA BASE DE DONNEES******************************/

        // Définir les coordonnées d'un polygone
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