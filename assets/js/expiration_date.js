document
    .getElementById("extend-30-days")
    .addEventListener("click", function () {
        // Obtener la fecha del input
        let dateInput = document.getElementById("expiration_date");
        let currentDateString = dateInput.value; // en formato YYYY-MM-DD

        // Convertir la fecha a un objeto Date
        let currentDate = new Date(currentDateString);

        // Añadir 30 días
        currentDate.setDate(currentDate.getDate() + 30);

        // Formatear la fecha al formato que necesita el input type="date", que es YYYY-MM-DD
        let newDateString = currentDate.toISOString().slice(0, 10);

        // Establecer la nueva fecha en el input
        dateInput.value = newDateString;
    });
