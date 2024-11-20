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
    }

    toggleView()
    {
        this.postForm.classList.toggle("hidden");
    }

    showView()
    {
        this.postForm.classList.remove("hidden");
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

        console.log(content);

        var request_parameter = {
            message: content,
            visibility: visibility
        }

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

    }

    onCancel()
    {
        this.hideView();
    }

}