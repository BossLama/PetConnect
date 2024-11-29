class PostFormController
{

    constructor(postForm)
    {
        if(document.getElementById(postForm) == null)
        {
            console.error("Unable to find the post form element with id " + postForm);
            return;
        }
        this.postForm = document.getElementById(postForm);
        this.postForm.querySelector(".button-submit").addEventListener("click", this.onSubmit.bind(this));
        this.postForm.querySelector(".button-cancel").addEventListener("click", this.onCancel.bind(this));
        document.getElementById("input_message_image").addEventListener("change", this.showImagePreview.bind(this));
    }

    toggleView()
    {
        this.postForm.classList.toggle("hidden");
        this.showImagePreview();
    }

    showView()
    {
        this.postForm.classList.remove("hidden");
    }

    showViewResponse(responseID)
    {
        this.postForm.classList.remove("hidden");
        document.getElementById("input_message_response").value = responseID;
    }

    showImagePreview()
    {
        var preview = document.getElementById("post_image_preview");
        var previewImg = preview.querySelector("img");
        var imageInput = document.getElementById("input_message_image");
        
        if(imageInput.files.length > 0)
        {
            var image = imageInput.files[0];
            var reader = new FileReader();
            reader.onload = function(e)
            {
                previewImg.src = e.target.result;
                preview.classList.remove("hidden");
            }
            reader.readAsDataURL(image);
        }
        else
        {
            preview.classList.add("hidden");
        }
    }

    hideView()
    {
        this.postForm.classList.add("hidden");
        this.postForm.reset();
    }

    onSubmit()
    {
        var content = document.getElementById("input_message").value;
        var visibility = document.getElementById("input_visibility").value;
        var response = document.getElementById("input_message_response").value;
        var isReport = document.getElementById("input_message_missing_report").checked;
        var imageInput = document.getElementById("input_message_image");
        var imageContent = null;

        if(response == "") response = null;

        if(imageInput.files.length > 0)
        {
            console.log("Image detected");
            var image = imageInput.files[0];
            var reader = new FileReader();
            reader.onload = function(e)
            {
                imageContent = e.target.result;
                // Base64 encoded image
                imageContent = imageContent.split(",")[1];

                var request_parameter = {
                    message: content,
                    visibility: visibility,
                    image: imageContent,
                    missing_report: isReport,
                    reply_to: response
                }
                this.onSendRequest(request_parameter);
            }.bind(this);
            reader.readAsDataURL(image);
        }
        else
        {
            console.log("No image");
            var request_parameter = {
                message: content,
                visibility: visibility,
                missing_report: isReport,
                reply_to: response
            }
            this.onSendRequest(request_parameter);
        }
    }

    onSendRequest(request_parameter)
    {
        console.log("Sending request");
        console.log(request_parameter);
        var request_body = {
            "endpoint_id": "post",
            "parameters": request_parameter
        }

        fetch(API_URL, {
            method: "POST",
            body: JSON.stringify(request_body),
            headers: {
                "Content-Type": "application/json",
                "Authorization": profileManager.getAuthToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.status === 'success') {
                this.hideView();
            } else {
                console.error(data.message);
                console.log(data);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }

    onCancel()
    {
        this.hideView();
    }

}