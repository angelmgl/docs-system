let profileBtn = document.querySelector(".profile-btn");
let profileNav = document.querySelector(".profile-nav");

profileBtn.addEventListener("click", () => {
    profileNav.classList.toggle("open");

    if (profileNav.classList.contains("open")) {
        setTimeout(() => {
            profileNav.classList.remove("open");
        }, 3000);
    }
});
