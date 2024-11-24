
let active_relationships = [];
let pending_relationships = [];
let recommended_profiles = [];

var user_id = null;

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
            user_id = data.user_id;
            data.data.forEach(profile => {
                renderProfile(profile);
            });

            if(active_relationships.length == 0) document.getElementById("friendlist_active").appendChild(getEmptyProfile());
            if(pending_relationships.length == 0) document.getElementById("friendlist_pending").appendChild(getEmptyProfile());
            if(recommended_profiles.length == 0) document.getElementById("friendlist_recommend").appendChild(getEmptyProfile());
        }
    });
}

// Render profile
function renderProfile(profile)
{
    if(profile.user_id == user_id) return;
    var user = document.createElement('div');
    user.className = 'user';

    var friendshipStatus = profile.relationship;

    user.innerHTML = `<div class="group">
                        <img src="./resources/placeholder/plaho_profile_dog.png" alt="Profilbild">
                        <div class="user-info">
                            <h3>`+ profile.username +`</h3>
                            <p>Hundebesitzer</p>
                            <p>Aus `+ profile.zip_code +`</p>
                        </div>
                    </div>`;
    user.appendChild(getRequestButton(friendshipStatus));
    
    appendUser(friendshipStatus, user);

}

// Append user to the friendlist
function appendUser(status, profile)
{
    switch(status)
    {
        case -1:
            document.getElementById("friendlist_recommend").appendChild(profile);
            recommended_profiles.push(profile);
            break;
        case 1:
            document.getElementById("friendlist_pending").appendChild(profile);
            pending_relationships.push(profile);
            break;
        case 2:
            document.getElementById("friendlist_active").appendChild(profile);
            active_relationships.push(profile);
            break;
    }
}

// Get empty profile
function getEmptyProfile()
{
    var user = document.createElement('div');
    user.className = 'user';
    user.innerHTML = "Schade, hier ist es leer.";
    return user;
}

// Returns the button for the friendship request
function getRequestButton(status)
{
    var button = document.createElement('button');
    switch(status)
    {
        case -1:
            button.innerHTML = "Freundschaft anfragen";
            break;
        case 1:
            button.innerHTML = "Freundschaft annehmen";
            button.classList.add("pending");
            break;
        case 2:
            button.innerHTML = "Freundschaft beenden";
            button.classList.add("active");
            break;
    }
    return button;
}


document.addEventListener('DOMContentLoaded', () => {
    loadProfiles();
});