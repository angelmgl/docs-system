const passwordInput = document.getElementById("password");
const submitBtn = document.getElementById("submit-btn");
const showPasswordInput = document.getElementById("show-password");

passwordInput.addEventListener("input", (e) => {
    if(e.target.value.length >= 8) {
        submitBtn.disabled = false;
        submitBtn.classList.remove("disabled");
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.add("disabled");
    }
})

showPasswordInput.addEventListener("change", () => {
    let showPassword = showPasswordInput.checked;

    passwordInput.type = showPassword ? "text" : "password";
})