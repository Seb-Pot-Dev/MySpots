const mobileChoiceMap = document.getElementById('mobile-choice-map');
const mobileChoiceList = document.getElementById('mobile-choice-list');
const mobileListSpotDesktopGlobal = document.getElementById('list-spots-desktop-global');
const mobileToggleFilters = document.getElementById('mobileToggleFilters');

if(IS_USER_LOGGED_IN){
    const mobileCloseFormSpot = document.getElementById('mobile-close-form-spot');
    const mobileAddSpotForm = document.getElementById('mobile-open-form-spot');
    mobileAddSpotForm.addEventListener("click", closeMobileAddSpot);
    // au click sur #mobile-close-form-spot

}


mobileChoiceList.addEventListener("click", showList)
mobileChoiceMap.addEventListener("click", showMap)
mobileToggleFilters.addEventListener('click', changeMobileToggleFiltersStyle)


function closeMobileAddSpot(){
    mobileAddSpotForm.classList.toggle('active');
}

function changeMobileToggleFiltersStyle(){
    mobileToggleFilters.classList.toggle('active')
}
function showList(){
    hideMap();
    addSelectedDuoList();
    mobileToggleFilters.style.display = "none"
    mapOptions.classList.toggle('active');
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

