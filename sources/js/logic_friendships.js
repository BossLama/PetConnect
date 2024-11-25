
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

    var friendshipStatus    = profile.relationship;
    var profile_picture     = profile.profile_picture;

    user.innerHTML = `<div class="group">
                        <img src="`+ profile_picture +`" alt="Profilbild">
                        <div class="user-info">
                            <h3>`+ profile.username +`</h3>
                            <p>Hundebesitzer</p>
                            <p>Aus `+ profile.zip_code +`</p>
                        </div>
                    </div>`;
    user.appendChild(getRequestButton(friendshipStatus, profile.user_id));
    
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
        case 3:
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
function getRequestButton(status, receiver)
{
    var button = document.createElement('button');
    button.onclick = function() { requestFriendship(receiver); };
    switch(status)
    {
        case -1:
            button.innerHTML = "Freundschaft anfragen";
            break;
        case 1:
            button.innerHTML = "Anfrage annehmen";
            button.classList.add("pending");
            break;
        case 2:
            button.innerHTML = "Freundschaft beenden";
            button.classList.add("active");
            break;
        case 3:
            button.innerHTML = "Anfrage zurückziehen";
            button.classList.add("pending");
            break;
    }
    return button;
}

// Request friendship
function requestFriendship(receiver)
{
    var parameters = {
        "receiver": receiver
    }

    fetch(API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': profileManager.getAuthToken()
        },
        body: JSON.stringify({
            "endpoint_id": "relationship",
            "parameters": parameters
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.status == "success") location.reload();
        if(data.status == "error")
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, data.message, 5000);
        }
    });
}




document.addEventListener('DOMContentLoaded', () => {
    loadProfiles();
});