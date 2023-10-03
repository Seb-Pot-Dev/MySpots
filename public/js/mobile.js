const mobileChoiceMap = document.getElementById('mobile-choice-map');
const mobileChoiceList = document.getElementById('mobile-choice-list');
const mobileListSpotDesktopGlobal = document.getElementById('list-spots-desktop-global');
const mobileToggleFilters = document.getElementById('mobileToggleFilters');

mobileChoiceList.addEventListener("click", showList)
mobileChoiceMap.addEventListener("click", showMap)

mobileToggleFilters.addEventListener('click', changeMobileToggleFiltersStyle)

function changeMobileToggleFiltersStyle(){
    mobileToggleFilters.classList.toggle('active')
}
function showList(){
    hideMap();
    addSelectedDuoList();
    mobileToggleFilters.style.display = "none"
}
function showMap(){
    hideList();
    addSelectedDuoMap();
    mobileToggleFilters.style.display = "unset"
}
function addSelectedDuoList(){
    mobileChoiceList.classList.add("selected");
    mobileChoiceMap.classList.remove("selectedLeft");
}
function addSelectedDuoMap(){
    mobileChoiceMap.classList.add("selectedLeft");
    mobileChoiceList.classList.remove("selected");
}

function hideMap(){
    document.getElementById('map').classList.add("hidden")
    mobileListSpotDesktopGlobal.style.display = "inherit"
}
function hideList(){
    document.getElementById('map').classList.remove("hidden")
    mobileListSpotDesktopGlobal.style.display = "none"
}



function toggleClassMobileActive(e){
    e.classList.toggle("mobile-active");
}

