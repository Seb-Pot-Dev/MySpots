// défini une variable marqueur quelconque pour le click sur la carte	
let marqueur;


// défini la manière dont la carte est centrée et le zoom
let map = L.map("map").setView([46.227638, 2.213749], 6);

// Tile layer de base CLASSIC (white)
var default_layer = L.tileLayer(
	"https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
	{
		maxZoom: 22,
		attribution:
			'&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
	}
);
// Tile layer de ThunderForest "Spinal Map" (HELL) (API KEY : 396269ca3cfb446d96f2e63dd998a5b9)
var Thunderforest_SpinalMap = L.tileLayer(
	"https://{s}.tile.thunderforest.com/spinal-map/{z}/{x}/{y}.png?apikey={apikey}",
	{
		attribution:
			'&copy; <a href="http://www.thunderforest.com/">Thunderforest</a>, &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
		apikey: "396269ca3cfb446d96f2e63dd998a5b9",
		maxZoom: 22,
	}
);
// Tile layer de Jawg Lab "DARK" (API KEY : UruJTpSBYzy3a17xRdwM4O3RGfWF3EWeeFJMUkQKcRHyyAlYMB5LEx7wxl4ppHd8)
var Jawg_Dark = L.tileLayer(
	"https://{s}.tile.jawg.io/jawg-dark/{z}/{x}/{y}{r}.png?access-token={accessToken}",
	{
		attribution:
			'<a href="http://jawg.io" title="Tiles Courtesy of Jawg Maps" target="_blank">&copy; <b>Jawg</b>Maps</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
		minZoom: 0,
		maxZoom: 22,
		subdomains: "abcd",
		accessToken:
			"UruJTpSBYzy3a17xRdwM4O3RGfWF3EWeeFJMUkQKcRHyyAlYMB5LEx7wxl4ppHd8",
	}
).addTo(map); // Définition du layer par défaut

var tileLayer1 = document.getElementById("layer-1");
var tileLayer2 = document.getElementById("layer-2");
var tileLayer3 = document.getElementById("layer-3");

function ChooseTileLayer1() {
	map.removeLayer(Jawg_Dark);
	map.removeLayer(default_layer);
	Thunderforest_SpinalMap.addTo(map);
}
function ChooseTileLayer2() {
	map.removeLayer(default_layer);
	map.removeLayer(Thunderforest_SpinalMap);
	Jawg_Dark.addTo(map);
}
function ChooseTileLayer3() {
	map.removeLayer(Thunderforest_SpinalMap);
	map.removeLayer(Jawg_Dark);
	default_layer.addTo(map);
}

tileLayer1.addEventListener("click", ChooseTileLayer1);
tileLayer2.addEventListener("click", ChooseTileLayer2);
tileLayer3.addEventListener("click", ChooseTileLayer3);

// GEOLOCALISATION
// Demande la géolocalisation de l'utilisateur et commence à suivre les mises à jour de position.
navigator.geolocation.watchPosition(success, error);

// en cas de succès
function success(userPosition) {
	// Lorsque la géolocalisation est obtenue avec succès, stocke la latitude et la longitude dans des variables globales.
	window.userLat = userPosition.coords.latitude;
	window.userLng = userPosition.coords.longitude;
	window.isUserLocationKnown = true;

	// // Obtient la précision des données de géolocalisation.
	// var accuracy = userPosition.coords.accuracy;

	//icon custom pour l'emplacement de l'utilisateur
	var youAreHereIcon = L.icon({
		iconUrl:
			"https://png.pngtree.com/png-vector/20230320/ourmid/pngtree-you-are-here-location-pointer-vector-png-image_6656543.png",
		iconSize: [60, 60],
		iconAnchor: [22, 94],
		popupAnchor: [-3, -76],
		shadowAnchor: [22, 94],
	});
	// Ajoute un marqueur sur la carte à l'emplacement de l'utilisateur.
	L.marker([userLat, userLng], {
		icon: youAreHereIcon,
		title: "Vous êtes ici",
		alt: 'Marqueur "vous êtes ici"',
	}).addTo(map);

	map.setView([userLat, userLng], 14);


	// Ajoute un cercle sur la carte représentant la précision de la position de l'utilisateur.
	// L.circle([userLat, userLng], { radius: accuracy }).addTo(map);
}

// en cas d'erreur
function error(err) {
	// En cas d'erreur lors de la géolocalisation, indique que la position de l'utilisateur n'est pas connue.
	window.isUserLocationKnown = false;
	if (err.code === 1) {
		alert(
			"Veuillez autoriser l'accès à la géolocalisation pour une expérience optimale."
		);
	} else {
		alert("Impossible d'obtenir la position actuelle pour le moment.");
	}
}

const btnAddUserLoc = document.querySelector("#add-user-pos");

function fillWithUserPosition(event) {
	event.preventDefault();
	// Si la position de l'utilisateur est connue, affiche les coordonnées dans le formulaire.
	if (isUserLocationKnown === true) {
		document.querySelector("#spot_lat").value = userLat;
		document.querySelector("#spot_lng").value = userLng;
	} else {
		promptGeolocationAccess();
	}
}
if (IS_USER_LOGGED_IN) {
	// Si click sur "Utiliser ma position", utiliser la fn fillWithUserPosition
	btnAddUserLoc.addEventListener("click", fillWithUserPosition);
}

// test
function promptGeolocationAccess() {
	const confirmation = window.confirm(
		"Pour utiliser cette fonction, veuillez activer la géolocalisation.\n\nCliquez sur 'OK' pour activer la géolocalisation ou 'Annuler' pour quitter."
	);

	if (confirmation) {
		// L'utilisateur a cliqué sur 'OK', vous pouvez tenter de demander la géolocalisation à nouveau.
		navigator.geolocation.watchPosition(success, error);
	} else {
		// L'utilisateur a cliqué sur 'Annuler' ou a fermé la fenêtre, vous pouvez faire quelque chose d'autre ou ne rien faire.
	}
}
// Si l'utilisateur est
if (IS_USER_LOGGED_IN) {
	/*********************************GESTION DU CLICK SUR LA MAP****************************************************************/
	//Appel la fonction onMapClick pour créer un Event lorsque l'on clique sur un endroit non marked de la map et indique les coordonnées
	map.on("click", onMapClick);

	//stockage de la fonction popup() de Leaflet dans la variable popup
	var popup = L.popup();

	// Lorsque je déselectionne un des champs 'spot_city, spot_cp etc..', utiliser la fonction getPosByAdress
	/* getPosByAdress permet de définir de définir un marqueur grâce a l'API Nominatim, 
    qui grâce a l'adresse permet de retrouver la latitude/longitude  */
	document
		.querySelector("#spot_city")
		.addEventListener("blur", getPosByAdress);
	document.querySelector("#spot_cp").addEventListener("blur", getPosByAdress);
	document
		.querySelector("#spot_adress")
		.addEventListener("blur", getPosByAdress);

	//Fonction pour créer un marqueur lorsque l'on clique sur un endroit de la map et renseigner les champs lat/lng du formulaire d'ajout de spot
	function onMapClick(e) {
		// on récupère les coordonnées du clic
		let pos = e.latlng;

		// on affiche les coordonnées dans le formulaire
		document.querySelector("#spot_lat").value = pos.lat;
		document.querySelector("#spot_lng").value = pos.lng;

		//pour créer un pop up qui indique les lat/lng de l'endroit cliqué
		popup
			.setLatLng(e.latlng)
			.setContent(
				"<a href='#' id='openFormAddSpotInPopup'>Nouveau Spot</a>"
			)
			.openOn(map);
    
    // Pour ouvrir le formulaire en cliquant sur le lien dans popup
    const openFormAddSpotInPopup = document.getElementById("openFormAddSpotInPopup");
    openFormAddSpotInPopup.addEventListener("click", toggleContainer);
	}
}
/**************** Ouvrir le form add spot par click sur popup *********************** */

// vars
const toggleForm = document.getElementById("toggleFormSpot");
const containerForm = document.getElementById("form-spot-id");

        // fn pour toggle
        function toggleContainer() {
            let computedStyle = window.getComputedStyle(containerForm);

            if (computedStyle.maxHeight !== "0px") {
                containerForm.style.maxHeight = "0px";
                containerForm.style.overflow = "hidden";
                toggleForm.innerHTML = "OUVRIR";
            } else {
                containerForm.style.maxHeight = "unset";
                containerForm.style.overflow = "unset";
                toggleForm.innerHTML = "FERMER";
                // Note: De même, ajustez le texte du bouton si nécessaire
            }
        }
		// Si l'utilisateur est connecté, on écoute l'event click sur le toggleContainer
		if(IS_USER_LOGGED_IN){
			// écouteurs d'événement:
			toggleForm.addEventListener("click", toggleContainer);
		}
        
//fonction pour récupérer la pos à partir de l'adresse entrée dans le formulaire de Spot (SpotType)
function getPosByAdress() {
	//On fabrique l'adresse
	let adresse =
		document.querySelector("#spot_adress").value +
		", " +
		document.querySelector("#spot_cp").value +
		" " +
		document.querySelector("#spot_city").value;

	// On initialise une requête AJAX
	const xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = () => {
		// Si la requête est terminée
		if (xmlhttp.readyState == 4) {
			// Si on a une une réponse
			if (xmlhttp.status == 200) {
				//on récupère la réponse
				let response = JSON.parse(xmlhttp.response);

				let lat = response[0]["lat"];
				let lng = response[0]["lon"];

				document.querySelector("#spot_lat").value = lat;
				document.querySelector("#spot_lng").value = lng;

				let pos = [lat, lng];
				popup
					.setLatLng(pos)
					.setContent(
						"<a href='#' id='openFormAddSpotInPopup'>Adresse renseignée</a>"
					)
					.openOn(map);

				map.setView(pos, 13);
			}
		}
	};

	// On ouvre la requête HTTP
	xmlhttp.open(
		"get",
		`https://nominatim.openstreetmap.org/search?q=${adresse}&format=json&addressdetails=1&limit=1&polygon_svg=1`
	);

	xmlhttp.send();
}

/************************************FIN GESTION DU CLICK SUR LA CARTE********************************************/
