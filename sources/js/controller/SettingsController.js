class SettingsController
{
    initFields()
    {
        profileManager.getProfileData((data) => {

            console.log(data);

            var username = data.username;
            var email    = data.email;
            var zip      = data.zip_code;

            document.getElementById("input_setting_username").value = username;
            document.getElementById("input_setting_email").value = email;
            document.getElementById("input_setting_zip").value = zip;
        });
    }
}


var settingsController = null;
document.addEventListener("DOMContentLoaded", () => {
    settingsController = new SettingsController();
    settingsController.initFields();
});