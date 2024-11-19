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
    .then(response => response.text())
    .then(data => {
        console.log(data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}