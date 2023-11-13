<?php 

if(isset($business)) { 
    // si existe usuario estamos en edit business
    $show_photo = $business["logo"] ? "show" : "";
    $show_input = !$business["logo"] ? "show" : "";
    $photo_url = get_logo($business);
    $default_background = "background-image: url($photo_url)";
} else { 
    // sino existe estamos en add business
    $show_input = "show";
    $default_background = "";
}

?>

<div class="input-wrapper file-input">
    <label for="logo">Logo de la empresa:</label>
    <div id="logo-container" class="<?php echo $show_photo; ?>" style="<?php echo $default_background; ?>">
        <button type="button" id="delete-logo" class="action delete">
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                <path fill="currentColor" d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
            </svg>
        </button>
    </div>
    <input type="file" id="logo" name="logo" class="<?php echo $show_input; ?>" accept=".jpg, .jpeg, .png, .webp">
</div>