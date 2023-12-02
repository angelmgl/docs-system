let btn = document.querySelector("#create-doc");
let nav = document.querySelector(".category-actions .profile-nav");

btn.addEventListener("click", () => {
    nav.classList.toggle("open");

    if (nav.classList.contains("open")) {
        setTimeout(() => {
            nav.classList.remove("open");
        }, 3000);
    }
});