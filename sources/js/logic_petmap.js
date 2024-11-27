var mapView = null;

document.addEventListener('DOMContentLoaded', function() {

    constructMap(1, 0);
    let profileManager = new ProfileManager();
    profileManager.getUserLocation((data) => {
        setMapLocation(data[0], data[1]);
        loadDoctors();
    });

});


function loadDoctors()
{
    fetch("./backend/storage/doctors.json")
    .then(response => response.json())
    .then(data => {
        data.forEach(element => {
            addClinic( element.name, element.latitude, element.longitude);
        });
    })
    .catch(error => {
        console.error('Error:', error);
    });
}