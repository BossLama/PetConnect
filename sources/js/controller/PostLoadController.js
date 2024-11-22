class PostLoadController
{
    lastIndex       = 0;
    limit           = 20;
    posts           = new Map();

    loadPosts(fromIndex = 0)
    {

        fetch(API_URL + "?endpoint_id=post&min=" + fromIndex + "&limit=" + this.limit, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Authorization": profileManager.getAuthToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status == "success")
            {
                data.data.forEach(post => {
                    this.renderPost(post);
                });
                this.lastIndex = fromIndex + data.data.length;
            }
            else
            {
                console.error(data.message);
                console.log(data);
            }
        })
    }


    renderPost(post)
    {
        var username        = post.creator.username;
        var posted_at       = post.posted_at;
        var visibility      = post.visibility;
        var message         = post.message;

        if (visibility == "0") visibility = "Privat";
        if (visibility == "1") visibility = "Freunde";
        if (visibility == "2") visibility = "Kommune";
        if (visibility == "3") visibility = "Öffentlich";

        var posted_at = new Date(posted_at);
        var now = new Date();
        var timeDifference = now - posted_at;
        var timeDifferenceInHours = timeDifference / 1000 / 60 / 60;
        var timeDifferenceInMinutes = timeDifference / 1000 / 60;

        var timeString = "";
        if (timeDifferenceInHours > 24) timeString = "vor " + Math.floor(timeDifferenceInHours / 24) + " Tagen";
        if (timeDifferenceInHours < 24) timeString = "vor " + Math.floor(timeDifferenceInHours) + " Stunden";
        if (timeDifferenceInMinutes < 60) timeString = "vor " + Math.floor(timeDifferenceInMinutes) + " Minuten";
        if (timeDifferenceInMinutes < 1) timeString = "vor wenigen Sekunden";
        
        var likeIcon            = "icon_light_favorite.svg";
        if(post.liked) likeIcon = "icon_red_favorite.svg";

        var likeClass = "like";
        if(post.liked) likeClass = "liked";

        var postElement = document.createElement("article");
        postElement.dataset.post_id = post.post_id;
        postElement.className = "post";

        postElement.innerHTML = `
            <div class="header">
                <img src="resources/placeholder/plaho_profile_dog.png" alt="Profile">
                <p class="username">`+ username +`</p>
                <p class="posted">`+ timeString +` - `+ visibility +`</p>
            </div>
            <div class="message">`+ message +`</div>
            <div class="controlls">
                <button class="button-controll like"><img src="resources/icons/`+ likeIcon +`" alt="Like"></button>
                <button class="button-controll comment"><img src="resources/icons/icon_light_comment.svg" alt="Comment"></button>
            </div>`;

        // Insert the post at the beginning of the post container
        var postContainer = document.getElementById("feed");
        var topScroll = postContainer.scrollTop;
        postContainer.insertBefore(postElement, postContainer.firstChild);

        // If feed scroll < 100px from top, scroll to top
        if (topScroll < 100) postContainer.scrollTop = 0;

        postElement.querySelector(".like").addEventListener("click", () => {
            interactController.addLike(post.post_id, postElement);
        });
    }
}

var postLoadController = null;
document.addEventListener('DOMContentLoaded', function() {
    postLoadController = new PostLoadController();
    postLoadController.loadPosts(0);

    setInterval(() => {
        postLoadController.loadPosts(postLoadController.lastIndex);
    }, 1000);
});