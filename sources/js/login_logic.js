var is_loading = false;


document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("button_register").addEventListener("click", onRegister);
});



function onRegister()
{
    if(is_loading) return;
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
            alert("Registration successful!");
        }
        else
        {
            alert("Registration failed: " + data.message);
        }
    })
}