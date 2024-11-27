var mapView = null;
var locationMarker = null;

// Init the mapView
function constructMap(lat, lon)
{
    mapView = L.map('map').setView([lat, lon], 16);
    L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(mapView);
}

function setMapLocation(lat, lon)
{
    mapView.setView([lat, lon], 16);
    if(locationMarker)
    {
        mapView.removeLayer(locationMarker);
    }
    locationMarker = L.marker([lat, lon]).addTo(mapView);
}

function addEvent(name, lat, lon) {
    const customIcon = L.icon({
      iconUrl: 'https://img.icons8.com/color/48/man-with-dog.png',
      iconSize: [40, 40],
      iconAnchor: [20, 40], 
      popupAnchor: [0, -40]
    });
  
    const marker = L.marker([lat, lon], { icon: customIcon }).addTo(mapView);

    marker.bindPopup(`<b>${name}</b>`).openPopup();
}
 
function addClinic(name, lat, lon) {
    const customIcon = L.icon({
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/403/403890.png',
      iconSize: [40, 40],
      iconAnchor: [20, 40], 
      popupAnchor: [0, -40]
    });
  
    const marker = L.marker([lat, lon], { icon: customIcon }).addTo(mapView);

    marker.bindPopup(`<b>${name}</b>`).openPopup();
}
