var mapView = null;

document.addEventListener('DOMContentLoaded', function() {

    constructMap(1, 0);
    let profileManager = new ProfileManager();
    profileManager.getUserLocation((data) => {
        setMapLocation(data[0], data[1]);
    });

});