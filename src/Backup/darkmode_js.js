if (typeof toggleButton == "undefined") {
	// définition du switch toggle-night-mode
	var toggleButton = document.getElementById("toggle-night-mode");
}
// Si click sur le switch dark mode, changer le fond en noir
toggleButton.addEventListener("click", () => {
	// Choix de la carte DARK
	ChooseTileLayer2();
});
// Si choix du dark mode, afficher en noir, sinon, en blanc
if (sessionStorage.getItem("nightMode") === "true") {
	ChooseTileLayer2();
} else {
	ChooseTileLayer3();
}
