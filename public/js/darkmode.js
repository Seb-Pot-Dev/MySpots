        // Définition des var
        const body = document.body;
        
        const toggleButton = document.getElementById("toggle-night-mode");
        

        // Si click sur le switch dark mode
        toggleButton.addEventListener("click", () => {
            // Ajout de la classe "night mode" sur le body
            body.classList.toggle("night-mode");
            
                // Si la classe night-mode est ajotuée au body, on stock en session une clé "night mode" avec la valeur "true"
                if (body.classList.contains("night-mode")) {
                    sessionStorage.setItem("nightMode", "true");
                    if(typeof ChooseTileLayer2 === 'function'){
                        ChooseTileLayer2();
                    }
                } else {
                //Sinon, on supprime la clé "night mode"
                    sessionStorage.removeItem("nightMode");
                    if(typeof ChooseTileLayer3 === 'function'){
                        ChooseTileLayer3();
                    }
                }
        });

            // Sur chaque page, on va vérifier la présence de la clé "sessionStorage" stockée en session
            document.addEventListener("DOMContentLoaded", () => {
                if (sessionStorage.getItem("nightMode") === "true") {
                    body.classList.add("night-mode");
                    // coche la bouton switch/toggle du darkmode 
                    document.getElementById("toggle-night-mode").checked = true;
                    if(typeof ChooseTileLayer2 === 'function'){
                        ChooseTileLayer2();
                    }
                }else{
                    document.getElementById("toggle-night-mode").checked = null;
                    if(typeof ChooseTileLayer3 === 'function'){
                        ChooseTileLayer3();
                    }
                }
            })
