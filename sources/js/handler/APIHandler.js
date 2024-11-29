const API_URL = "./backend/index.php";

document.addEventListener("DOMContentLoaded", function() {

    createMobileButton();
});


function createMobileButton()
{
    if(window.location.pathname.includes('login.html')) return;

    var blurView = document.createElement("div");
    blurView.classList.add("blur-view");
    blurView.classList.add("hidden");
    document.body.appendChild(blurView);

    blurView.addEventListener("click", function() {
        var sideNav = document.querySelector(".side-nav");
        sideNav.classList.remove("mobile-show");
        blurView.classList.add("hidden");
    });

    var button = document.createElement("button");
    button.classList.add("side-nav-mobile-toggle");
    button.innerHTML = '<img src="./resources/icons/icon_light_menu.svg" alt="Menu">';

    button.addEventListener("click", function() {
        var sideNav = document.querySelector(".side-nav");
        sideNav.classList.toggle("mobile-show");
        blurView.classList.toggle("hidden");
    });

    document.body.appendChild(button);
}