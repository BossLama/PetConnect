
// Load profiles from the server
function loadProfiles()
{
    fetch(API_URL + "?endpoint_id=profile&load_all=true", {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': profileManager.getAuthToken()
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
    });
}

// Render profile
function renderProfile(profile)
{
    
}


document.addEventListener('DOMContentLoaded', () => {
    loadProfiles();
});