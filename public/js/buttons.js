/****** DOUBLE STEP BUTTON (avec confirmation)********* */
var deleteButtons = document.querySelectorAll(".delete-button");
deleteButtons.forEach(function(deleteButton) {
    deleteButton.addEventListener("click", function(event) {
        event.preventDefault();
        if (deleteButton.innerHTML === '<i class="fa-regular fa-trash-can"></i>') {
            deleteButton.textContent = "Sûr?";
            deleteButton.classList.add("delete-button-confirm");
        } else if (deleteButton.textContent === "Sûr?") {
            // Remplacez cette ligne par l'action de suppression réelle
            // window.location.href = "{{ path('deletePicture_admin', {'idSpot': spot.id, 'idPic': image.id}) }}";
        }
    });
});