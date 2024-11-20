const ENABLE_DEBUG          = true;

var postFormController      = null;
let is_page_loaded          = false;

// Print debug message to console if ENABLE_DEBUG is true
function debugLog(message)
{
    if (ENABLE_DEBUG) console.log(message);
}

// Executes the start process afer the page has been loaded
document.addEventListener('DOMContentLoaded', function() {
    if(is_page_loaded) return;
    debugLog("Page loaded");
    is_page_loaded = true;
    onBoot();
});

// Executed after first page load
function onBoot()
{
    postFormController = new PostFormController("post_form");
    postFormController.hideView();

    // Add event listener to show post form
    document.getElementById("button_show_post").addEventListener("click", function() {
        postFormController.showView();
    });
}