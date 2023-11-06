const profilePictureContainer = document.getElementById("profile-picture-container");
const deleteProfilePicture = document.getElementById("delete-profile-picture");
const profilePictureInput = document.getElementById("profile_picture");
const oldPhotoInput = document.getElementById("old_photo");

deleteProfilePicture.addEventListener("click", e => {
    if(oldPhotoInput) {
        oldPhotoInput.value = "";
    }
    profilePictureContainer.classList.remove("show");
    profilePictureInput.classList.add("show");

    profilePictureInput.value = "";
})

profilePictureInput.addEventListener("change", function() {
    const file = this.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(event) {
            // Establece la imagen cargada como fondo de div#preview
            profilePictureContainer.style.backgroundImage = `url(${event.target.result})`;
            profilePictureContainer.classList.add("show");
            profilePictureInput.classList.remove("show");
        };

        reader.readAsDataURL(file);
    }
});
