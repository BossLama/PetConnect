// Load profiles from the server
function loadNotifications()
{
    fetch(API_URL + "?endpoint_id=notification", {
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
            data.notifications.forEach(profile => {
                renderProfile(profile);
            });

            if(data.notifications.length == 0) document.getElementById("notifications").appendChild(getEmptyNotification());

        }
    });
}

// Render profile
function renderProfile(profile)
{
    console.log(profile);
    var createdAt = new Date(profile.created_at);
    var date = createdAt.getDate() + "." + (createdAt.getMonth() + 1) + "." + createdAt.getFullYear();

    var user = document.createElement('div');
    user.className = 'user';
    user.innerHTML = `<p class="notification_date_label">`+ date +`</p><p>` + profile.message + `</p>`;
    document.getElementById("notifications").appendChild(user);
}


// Get empty profile
function getEmptyNotification()
{
    var user = document.createElement('div');
    user.className = 'user';
    user.innerHTML = "Derzeit keine neuen Benachrichtigungen";
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
    loadNotifications();
});