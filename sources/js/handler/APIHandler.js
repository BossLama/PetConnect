const API_URL = "./backend/index.php";

document.addEventListener("DOMContentLoaded", function() {

    createMobileButton();
});


function createMobileButton()
{
    var button = document.createElement("button");
    button.classList.add("side-nav-mobile-toggle");
    button.innerHTML = '<img src="./resources/icons/icon_light_menu.svg" alt="Menu">';

    button.addEventListener("click", function() {
        var sideNav = document.querySelector(".side-nav");
        sideNav.classList.toggle("mobile-show");
    });

    document.body.appendChild(button);
}