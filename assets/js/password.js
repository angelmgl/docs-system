const passwordInput = document.getElementById("password");
const passwordRepeat = document.getElementById("password_repeat");
const pwMessage = document.getElementById("pw-message");
const submitBtn = document.getElementById("submit-btn");
const showPasswordInput = document.getElementById("show-password");

passwordInput.addEventListener("input", (e) => {
    const pw = passwordRepeat.value;
    if(e.target.value.length >= 8 && e.target.value === pw) {
        submitBtn.disabled = false;
        submitBtn.classList.remove("disabled");
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.add("disabled");
    }
})

passwordRepeat.addEventListener("input", (e) => {
    const pw = passwordInput.value;
    if(e.target.value.length >= 8 && e.target.value === pw) {
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
    passwordRepeat.type = showPassword ? "text" : "password";
})