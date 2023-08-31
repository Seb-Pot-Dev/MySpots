    // AJAX_spot_filters_search.js
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("search-form");

        
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(form);
    
            const xhr = new XMLHttpRequest();
            
            xhr.open("POST", "/spot/search", true);  // Point to the "search" route
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    const updatedList = JSON.parse(xhr.responseText);
                    
                    // Code a executer pour changer le contenu de la liste de spots
                    const spotListElement = document.getElementById("list-spots");
                    // Clear out the existing list
                    while (spotListElement.firstChild) {
                        spotListElement.removeChild(spotListElement.firstChild);
                    }
                    // Populate with new list
                    updatedList.forEach((spot) => {
                        const listItem = document.createElement('li');
                        listItem.textContent = spot.name; // Assuming 'name' is a field on your spot objects
                        spotListElement.appendChild(listItem);
                    });


                } else {
                    console.error("Server responded with an error : ", xhr.responseText);
                }
            };
            xhr.onerror = function() {
                console.error("Request failed");
            };
    
            xhr.send(formData);
        });
    });
    
