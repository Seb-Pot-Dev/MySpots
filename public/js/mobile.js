var mobileChoiceMap = document.getElementById('mobile-choice-map');
var mobileChoiceList = document.getElementById('mobile-choice-list');


mobileChoiceList.addEventListener("click", showList)
mobileChoiceMap.addEventListener("click", showMap)

function showList(){
    hideMap();
}
function showMap(){
    hideList();
}

function hideMap(){
    document.getElementById('map').classList.add("hidden")
    document.getElementById('list-spots-desktop-global').style.display = "inherit"
}
function hideList(){
    document.getElementById('map').classList.remove("hidden")
    document.getElementById('list-spots-desktop-global').style.display = "none"
}