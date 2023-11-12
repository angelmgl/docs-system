const logoContainer = document.getElementById("logo-container");
const deleteLogo = document.getElementById("delete-logo");
const logoInput = document.getElementById("logo");
const oldPhotoInput = document.getElementById("old_photo");

deleteLogo.addEventListener("click", e => {
    if(oldPhotoInput) {
        oldPhotoInput.value = "";
    }
    logoContainer.classList.remove("show");
    logoInput.classList.add("show");

    logoInput.value = "";
})

logoInput.addEventListener("change", function() {
    const file = this.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(event) {
            // Establece la imagen cargada como fondo de div#preview
            logoContainer.style.backgroundImage = `url(${event.target.result})`;
            logoContainer.classList.add("show");
            logoInput.classList.remove("show");
        };

        reader.readAsDataURL(file);
    }
});
