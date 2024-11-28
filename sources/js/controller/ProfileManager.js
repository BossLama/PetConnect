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
        return "Bearer " + localStorage.getItem('authToken');
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
        fetch(API_URL + "?endpoint_id=profile", {
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

    isAuthorized(callback)
    {
        fetch(API_URL + "?endpoint_id=auth&token=" + this.getAuthToken(), {
            method: 'GET',
            headers: {
                'Content-type': 'application/json',
                'Authorization': this.getAuthToken()
            },    
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                callback(true);
            } else {
                callback(false);
                //console.error(data.message);
                //console.log(data);
            }
        });
    }

    getUserLocation(callback)
    {
        this.getProfileData((data) => {
            let zip = data.zip_code.split(' ')[0];
            fetch(API_URL + "?endpoint_id=zipcodestack&zip=" + zip, {
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

        let profilePictures = document.querySelectorAll('.own-profile-picture');
        profilePictures.forEach(picture => {
            picture.src = user.profile_picture;
        });
    }
}


document.addEventListener('DOMContentLoaded', function() {
    let profileManager = new ProfileManager();
    if(!profileManager.hasAuthToken())
    {
        redirectToLogin();
        return;
    }

    setInterval(() => {
        profileManager.isAuthorized((authorized) => {
            if(!authorized)
            {
                redirectToLogin();
            }
        });
    }, 1000);

    profileManager.isAuthorized((authorized) => {
        if(!authorized)
        {
            redirectToLogin();
            return;
        }
        else
        {
            profileManager.getProfileData(function(data) {
                profileManager.renderProfileData(data);
            });
        
            profileManager.getUserLocation((location) => console.log(location));
        }
    });

});


function redirectToLogin()
{
    if(!window.location.pathname.includes('login.html'))
    {
        window.location.href = './login.html';
    }
}