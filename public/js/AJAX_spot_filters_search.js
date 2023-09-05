// AJAX_spot_filters_search.js

// Ajouter un événement qui se déclenche après le chargement complet du document
document.addEventListener("DOMContentLoaded", function () {
	// Sélectionner le formulaire ayant l'ID "search-form"
	const form = document.getElementById("search-form");

	// Ajouter un écouteur d'événements sur le formulaire lors de sa soumission
	form.addEventListener("submit", function (event) {
		// Empêcher le comportement par défaut de la soumission du formulaire
		event.preventDefault();

		// Récupérer toutes les données du formulaire
		const formData = new FormData(form);

		// Créer un nouvel objet XMLHttpRequest
		const xhr = new XMLHttpRequest();

		// Configurer la requête pour envoyer les données en POST vers "/spot/search"
		xhr.open("POST", "/spotController/search", true); // Direction vers la route "search"
		// console.log(xhr)
		// Définir une en-tête pour indiquer que la requête est une requête AJAX
		xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

		// Ajouter un écouteur d'événements qui se déclenche une fois la requête chargée
		xhr.onload = function () {
			// Si la requête est un succès (code statut entre 200 et 399)
			if (xhr.status >= 200 && xhr.status < 400) {
				console.log(xhr.responseText);
				// Convertir la réponse texte en objet JSON
				const updatedList = JSON.parse(xhr.responseText);

				// Récupérer l'élément HTML ayant l'ID "list-spots"
				const spotListElement = document.getElementById("list-spots");

				// Vider la liste actuelle
				while (spotListElement.firstChild) {
					spotListElement.removeChild(spotListElement.firstChild);
				}

				// Afficher la nouvelle liste mise à jour
				updatedList.forEach((spot) => {
					// Créer un nouvel élément 'li' pour chaque 'spot' dans la liste mise à jour
					const listItem = document.createElement("li");

					// Supposer que chaque objet 'spot' a un champ 'name'
					listItem.textContent = spot.name;

					// Ajouter le nouvel élément 'li' à la liste
					spotListElement.appendChild(listItem);
				});
			} else {
				// Afficher une erreur dans la console si la requête retourne un code d'erreur
				console.error("Server responded with an error");
			}
		};

		// Ajouter un écouteur d'événements qui se déclenche en cas d'erreur de requête
		xhr.onerror = function () {
			console.error("Request failed");
		};

		// Envoyer la requête avec les données du formulaire
		xhr.send(formData);
	});
});
