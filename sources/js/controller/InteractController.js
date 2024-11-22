class InteractController
{
    // Add a like to a post
    addLike(id, element)
    {
        var parameters = {
            "interaction": 1,
            "post_id": id
        }

        fetch(API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": profileManager.getAuthToken()
            },
            body: JSON.stringify({
                endpoint_id: "interact",
                parameters: parameters
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status == "success")
            {
                if(data.type == "added")
                {
                    element.querySelector(".like").querySelector("img").src = "resources/icons/icon_red_favorite.svg";
                }
                else if(data.type == "removed")
                {
                    element.querySelector(".like").querySelector("img").src = "resources/icons/icon_light_favorite.svg";
                }
            }
            else
            {
                console.error(data);
                console.error(data.message);
            }
        })
    }
}


var interactController;
document.addEventListener("DOMContentLoaded", () => {
    interactController = new InteractController();
});