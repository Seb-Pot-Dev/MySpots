var mobileChoiceMap = document.getElementById('mobile-choice-map');
var mobileChoiceList = document.getElementById('mobile-choice-list');
var mobileListSpotDesktopGlobal = document.getElementById('list-spots-desktop-global');

var mobileToggleFilters = document.getElementById('mobile-toggle-filters');

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

function toggleClassMobileActive(e){
    e.classList.toggle("mobile-active");
}