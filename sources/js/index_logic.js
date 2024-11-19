let nextIndexPost = 0;              // The next index of the post to load

document.addEventListener('DOMContentLoaded', function() {
    getPosts();
});

// Load the posts the user can see
function getPosts()
{
    fetch(api_url + "?endpoint_id=post", {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            'Authorization': profileManager.getAuthToken()
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        data.data.forEach(post => {
            renderPost(post);
        });
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function renderPost(post)
{
    let username    = post.creator.username;
    let postDate    = post.posted_at;
    let message     = post.message;

    // Calculate the time ago
    let timeAgo = Math.floor((new Date() - new Date(postDate)) / 1000 / 60 / 60);
    let timeAgoLabel = "vor " + timeAgo + " Stunden";
    if(timeAgo == 0)
    {
        timeAgo = Math.floor((new Date() - new Date(postDate)) / 1000 / 60);
        timeAgoLabel = "vor " + timeAgo + " Minuten";

        if(timeAgo == 0) timeAgoLabel = "vor wenigen Sekunden";
    }

    let category    = "#unbekannt";
    if(post.visibility == 0) category = "#privat"
    if(post.visibility == 1) category = "#freunde"
    if(post.visibility == 2) category = "#kommune"
    if(post.visibility == 3) category = "#community"

    let postElement = document.createElement("div");
    postElement.classList.add("post");
    let html = `
                <div class="header">
                    <img src="media/images/profiles/user_4973454_example.jpg" alt="">
                    <div class="details">
                        <p class="group">`+ category +`</p>
                        <div class="meta"><p class="name">`+ username +`</p><p class="date">`+ timeAgoLabel +`</p></div>
                    </div>
                </div>
                <div class="content"><p>`+ message + `</p></div>
                <div class="action-bar">
                    <button>Das ist lustig</button>
                    <button>Antworten</button>
                    <button>Teilen</button>
                </div>
    `;
    postElement.innerHTML = html;
    // Insert the post into the feed on top
    document.getElementById("feed").insertAdjacentHTML('afterbegin', postElement.outerHTML);
}