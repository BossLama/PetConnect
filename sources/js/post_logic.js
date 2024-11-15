let textarea_description = null;
let button_submit = null;
let loading = false;

document.addEventListener('DOMContentLoaded', function() {
    textarea_description = document.getElementById('input_post_text');
    button_submit = document.getElementById('button_submit');
    button_submit.addEventListener('click', createPost);

    textarea_description.addEventListener('input', checkCanSubmit);
});

// Check if the description is long enough to submit
function checkCanSubmit()
{
    if(textarea_description.value.length > 10)
    {
        button_submit.disabled = false;
        return true;
    }
    else
    {
        button_submit.disabled = true;
        return false;
    }
}

// Create a new post
function createPost()
{
    if(!checkCanSubmit())
    {
        const unicornManager =  new UnicornAlertHandler();
        unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Schreibe einen Beitrag mit min. 10 Zeichen.', 5000);
        return;
    }

    var input_visiblitity   = document.getElementById('input_post_visible');
    var input_category      = document.getElementById("input_post_category");
    var input_text          = document.getElementById('input_post_text');
    var input_post_image    = document.getElementById('input_post_image');  

    var parameters = {
        "visibility"    : input_visiblitity.value,
        "category"      : input_category.value,
        "message"       : input_text.value
    }

    if(input_post_image.files.length > 0)
    {
        var file = input_post_image.files[0];
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function() {
            parameters["image"] = reader.result;
            sendPost(parameters);
        }
    }
    else
    {
        sendPost(parameters);
    }
}

// Send the post to the server
function sendPost(parameters)
{

    if(loading)
    {
        const unicornManager =  new UnicornAlertHandler();
        unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Bitte warte bis der vorherige Beitrag gesendet wurde.', 5000);
        return;
    }
    loading = true;

    var body = {
        "endpoint_id"   : "post",
        "parameters"    : parameters
    }

    fetch(api_url, {
        method: 'POST',
        headers: {
            'Content-type': 'application/json',
            'Authorization': profileManager.getAuthToken()
        },
        body: JSON.stringify(body)
    })
    .then(response => response.json())
    .then(data => {
        if(data.status == "success")
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.INFO, 'Dein Beitrag wurde erfolgreich erstellt.', 5000);
            window.location.href = 'index.html';
        }
        else
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, data.message, 5000);
        }
        loading = false;
    })
    .catch((error) => {
        loading = false;
        const unicornManager =  new UnicornAlertHandler();
        unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Beim Erstellen des Beitrags ist ein Fehler aufgetreten.', 5000);
    })
}