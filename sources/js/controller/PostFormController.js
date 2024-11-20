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

    }

    onCancel()
    {
        this.hideView();
    }

}