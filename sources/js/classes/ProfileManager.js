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
}