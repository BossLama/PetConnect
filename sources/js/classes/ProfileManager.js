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


    renderProfileData(user)
    {

    }
}


document.addEventListener('DOMContentLoaded', function() {
    let profileManager = new ProfileManager();
    if(!profileManager.hasAuthToken()) return;

    profileManager.getProfileData(function(data) {
        profileManager.renderProfileData(data);
    });
});