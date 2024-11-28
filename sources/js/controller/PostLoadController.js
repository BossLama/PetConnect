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

        console.log(post);

        var username        = post.creator.username;
        var profile_picture = post.creator.profile_picture;
        var posted_at       = post.posted_at;
        var visibility      = post.visibility;
        var message         = post.message;
        var missing_report  = post.missing_report;

        if (visibility == "0") visibility = "Privat";
        if (visibility == "1") visibility = "Freunde";
        if (visibility == "2") visibility = "Kommune";
        if (visibility == "3") visibility = "Öffentlich";

        var missing_report_class = "";
        if (missing_report) missing_report_class = "missing-report";

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

        var likeCount = post.likes.length;

        var postElement = document.createElement("article");
        postElement.dataset.post_id = post.post_id;
        postElement.className = "post " + missing_report_class;

        var isReply = post.reply_to != null;
        var reply_to_message = "";

        if(isReply)
        {
            var reply_box = document.createElement("div");
            reply_box.className = "reply_msg";

            reply_box.innerHTML = ""+  post.reply_to.message;
            reply_to_message = reply_box.outerHTML;
        }


        postElement.innerHTML = `
            <div class="header">
                <img src="`+ profile_picture +`" alt="Profile">
                <p class="username">`+ username +`</p>
                <p class="posted">`+ timeString +` - `+ visibility +`</p>
            </div>
            `+ reply_to_message +`
            <div class="message">`+ message +`</div>
            <div class="controlls">
                <button class="button-controll like"><img src="resources/icons/`+ likeIcon +`" alt="Like">
                <p class="like-count">`+ likeCount +`</p></button>
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

        postElement.querySelector(".comment").addEventListener("click", () => {
            postFormController.showViewResponse(post.post_id);
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