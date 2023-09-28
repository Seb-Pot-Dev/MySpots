/*************POUR LES FILTRES ********** */
const toggleFilters = document.getElementById("toggleFilters");

toggleFilters.addEventListener("click", function () {
	const container = document.getElementById("search-form");
	const arrows = document.querySelectorAll(".arrow-toggle-filters");
	// SI #search-form n'est pas en display none
	if (container.style.display !== "none") {
		// le faire passer en display none
		container.style.display = "none";
		toggleFilters.style.top = "100px";
		arrows.forEach(function (arrow) {
			arrow.innerHTML = "OUVRIR FILTRES";
			// Do something with each arrow element
		});
	} else {
		toggleFilters.style.top = "474px";
		container.style.display = "block";
		container.style.maxHeight = "100%"; // Rétablissez ceci à votre max-width initial*
		arrows.forEach(function (arrow) {
			// Do something with each arrow element
			arrow.innerHTML = "FERMER FILTRES";
		});
	}
});

/**************** MEME LOGIQUE POUR LA LISTE DES SPOTS *********************** */
const toggleList = document.getElementById("toggleListSpots");
const containerList = document.getElementById("list-spots-desktop-id");

toggleList.addEventListener("click", function () {
	if (containerList.style.display !== "none") {
		containerList.style.display = "none";
		toggleList.style.top = "92px";
		toggleList.innerHTML = "OUVRIR LISTE";
	} else {
		containerList.style.display = "block";

		toggleList.style.top = "474px";
		containerList.style.maxHeight = "350px"; // Rétablissez ceci à votre max-width initial*
		toggleList.innerHTML = "FERMER LISTE";
	}
});

