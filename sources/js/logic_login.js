const ENABLE_DEBUG          = true;

var unicornManager          = null;
var login_form              = null;
var register_form           = null;

var button_login            = null;
var button_register         = null;

let is_page_loaded          = false;
let has_pending_request     = false;

// Print debug message to console if ENABLE_DEBUG is true
function debugLog(message)
{
    if (ENABLE_DEBUG) console.log(message);
}

// Executes the start process afer the page has been loaded
document.addEventListener('DOMContentLoaded', function() {
    if(is_page_loaded) return;
    debugLog("Page loaded");
    is_page_loaded = true;
    onBoot();
});

// Executed after first page load
function onBoot()
{
    unicornManager      =  new UnicornAlertHandler();
    login_form          = document.getElementById("form_login");
    register_form       = document.getElementById("form_register");
    button_login        = document.getElementById("button_login");
    button_register     = document.getElementById("button_register");

    document.getElementById("input_register_zip").addEventListener("focusout", checkZipCode);
    document.getElementById("input_register_zip").addEventListener("focusin", function() {
        var zip = document.getElementById("input_register_zip").value;
        var zip = zip.split(" ")[0];
        document.getElementById("input_register_zip").value = zip;
    });
    document.getElementById("input_register_zip").addEventListener("keydown", function(event) {
        if(event.keyCode == 32 || event.keyCode == 13)
        {
            event.preventDefault();
            this.blur();
            checkZipCode();
        }
        if(isNaN(event.key) && event.key != "Backspace")
        {
            event.preventDefault();
        }
    });

    button_login.addEventListener("click", () => onLogin(null));
    button_register.addEventListener("click", onRegister);

    document.querySelectorAll(".button-toggle").forEach(function(button) {
        button.addEventListener("click", toggleForm);
    });
}

// Switches between login and register form
function toggleForm()
{
    if(login_form.classList.contains("hidden"))
    {
        login_form.classList.remove("hidden");
        register_form.classList.add("hidden");
    }
    else
    {
        login_form.classList.add("hidden");
        register_form.classList.remove("hidden");
    }
}

// Login the user
function onLogin(totp)
{
    var email      = document.getElementById("input_login_email");
    var password   = document.getElementById("input_login_password");

    var request_parameter = {
        email: email.value,
        password: password.value,
        totpCode: totp
    }

    var request_body = {
        "endpoint_id": "auth",
        "parameters": request_parameter
    }

    fetch(API_URL, {
        method: "PUT",
        body: JSON.stringify(request_body),
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if(data.status == "success")
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.SUCCESS, "Sie sind nun angemeldet", 5000);
            profileManager.setAuthToken(data.token);
            window.location.href = "index.html";
        }
        else
        {
            if(data.code == -1)
            {
                var userID = data.user_id;
                twoFactorController.displayTwoFactorView(userID, (totp, result) => {
                    if(result)
                    {
                        twoFactorController.removeTwoFactorView();
                        onLogin(totp);
                    }
                });
                return;
            }
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, data.message, 5000);
        }
    });
}


// Is called when the user clicks on the register button
function onRegister()
{
    if(has_pending_request)
    {
        const unicornManager =  new UnicornAlertHandler();
        unicornManager.createAlert(UnicornAlertTypes.ERROR, "Es werden gerade Daten geladen...", 5000);
        return;
    }
    var mail        = document.getElementById("input_register_email");
    var password    = document.getElementById("input_register_password");
    var username    = document.getElementById("input_register_username");
    var zip         = document.getElementById("input_register_zip");
    var pet         = document.getElementById("input_register_pet");

    if(pet.value == "none")
    {
        const unicornManager =  new UnicornAlertHandler();
        unicornManager.createAlert(UnicornAlertTypes.ERROR, "Bitte wählen Sie ein Haustier aus", 5000);
        return;
    }

    var request_parameter = {
        email: mail.value,
        password: password.value,
        username: username.value,
        zip: zip.value,
        pet: pet.value
    }

    var request_body = {
        "endpoint_id": "auth",
        "parameters": request_parameter
    }

    fetch(API_URL, {
        method: "POST",
        body: JSON.stringify(request_body),
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        has_pending_request = false;
        if(data.status == "success")
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.INFO, 'Sie sind erfolgreich registriert', 5000);
            profileManager.setAuthToken(data.token);
            window.location.href = "index.html";
        }
        else
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, data.message, 5000);
        }
    })
}

// Checks if the zip code is valid
function checkZipCode()
{
    if(has_pending_request) return;
    var zip         = document.getElementById("input_register_zip").value;
    is_loading = true;

    fetch(API_URL + "?endpoint_id=zipcodestack&zip=" + zip, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        has_pending_request = false;
        if(data.status == "success")
        {
            var postalcode = data.city.postal_code;
            var city = data.city.city;
            document.getElementById("input_register_zip").value = postalcode + " " + city;
        }
        else
        {
            if(zip == "") return;
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Diese Postleitzahl kennen wir nicht', 5000);
            document.getElementById("input_register_zip").focus();
            document.getElementById("input_register_zip").value = "";
        }
    })
}