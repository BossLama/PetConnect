var profileManager;
document.addEventListener('DOMContentLoaded', function() {
    profileManager = new ProfileManager();
});


// Class to manage the user profile
class ProfileManager
{
    setAuthToken(token)
    {
        localStorage.setItem('authToken', token);
    }

    getAuthToken()
    {
        return localStorage.getItem('authToken');
    }

    hasAuthToken()
    {
        return localStorage.getItem('authToken') !== null;
    }

    removeAuthToken()
    {
        localStorage.removeItem('authToken');
    }

    getProfileData(callback)
    {
        fetch(api_url + "?endpoint_id=profile", {
            method: 'GET',
            headers: {
                'Content-type': 'application/json',
                'Authorization': this.getAuthToken()
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                callback(data.data);
            } else {
                console.error(data.message);
                console.log(data);
            }
        })
    }

    getUserLocation(callback)
    {
        this.getProfileData((data) => {
            let zip = data.zip_code.split(' ')[0];
            fetch(api_url + "?endpoint_id=zipcodestack&zip=" + zip, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.status == "success")
                {
                    callback([data.city.latitude, data.city.longitude]);
                }
            })
        });
    }

    renderProfileData(user)
    {
        let usernameLabels = document.querySelectorAll('.username-label');
        usernameLabels.forEach(label => {
            label.innerHTML = user.username;
        });
    }
}


document.addEventListener('DOMContentLoaded', function() {
    let profileManager = new ProfileManager();
    if(!profileManager.hasAuthToken())
    {
        if(!window.location.pathname.includes('login.html'))
        {
            window.location.href = './login.html';
        }
        return;
    }

    profileManager.getProfileData(function(data) {
        profileManager.renderProfileData(data);
    });

    profileManager.getUserLocation((location) => console.log(location));
});