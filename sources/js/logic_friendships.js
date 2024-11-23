
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

        if(data.status == "success")
        {
            data.data.forEach(profile => {
                renderProfile(profile);
            });
        }
    });
}

// Render profile
function renderProfile(profile)
{
    var user = document.createElement('div');
    user.className = 'user';

    var friendshipStatus = profile.friendship;
    var friendshipButton = friendshipStatus == 2 ? "Freunschaft beenden" : "Freunschaft anfragen";
    var friendschaftButtonClass = friendshipStatus == 2 ? "active" : "";

    user.innerHTML = `
                <div class="group">
                    <img src="./resources/placeholder/plaho_profile_dog.png" alt="Profilbild">
                    <div class="user-info">
                        <h3>`+ profile.username +`</h3>
                        <p>Hundebesitzer</p>
                        <p>Aus 85247 Schwabhausen</p>
                    </div>
                </div>
                <button class="`+ friendschaftButtonClass +`">`+ friendshipButton +`</button>
            `;
    
    if(friendshipStatus == 2)
    {
        document.getElementById("friendlist_active").appendChild(user);
    }
    else
    {
        document.getElementById("friendlist_recommend").appendChild(user);
    }

}


document.addEventListener('DOMContentLoaded', () => {
    loadProfiles();
});