var is_loading = false;


document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("button_register").addEventListener("click", onRegister);

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

});

// Check the entered zip code
function checkZipCode()
{
    if(is_loading) return;
    var zip         = document.getElementById("input_register_zip").value;
    is_loading = true;

    fetch(api_url + "?endpoint_id=zipcodestack&zip=" + zip, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        is_loading = false;
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


// Is called when the user clicks on the register button
function onRegister()
{
    if(is_loading)
    {
        const unicornManager =  new UnicornAlertHandler();
        unicornManager.createAlert(UnicornAlertTypes.ERROR, "Es werden gerade Daten geladen...", 5000);
    }
    var mail        = document.getElementById("input_register_email");
    var password    = document.getElementById("input_register_password");
    var username    = document.getElementById("input_register_username");
    var zip         = document.getElementById("input_register_zip");

    var request_parameter = {
        email: mail.value,
        password: password.value,
        username: username.value,
        zip: zip.value
    }

    var request_body = {
        "endpoint_id": "auth",
        "parameters": request_parameter
    }

    fetch(api_url, {
        method: "POST",
        body: JSON.stringify(request_body),
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        is_loading = false;
        if(data.status == "success")
        {
            document.getElementById("select_pet_view").classList.remove("hidden");

            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.INFO, 'Sie sind erfolgreich registriert', 5000);
        }
        else
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, data.message, 5000);
        }
    })
}