// Genera el nombre de usuario basado en el nombre y apellido del empleado seleccionado
function generateUsername() {
    const employeeSelect = document.getElementById("employee_id_add");
    const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];

    const firstName = selectedOption.getAttribute("data-firstname");
    const lastName = selectedOption.getAttribute("data-lastname");

    if (firstName && lastName) {
        const baseUsername =
            firstName.charAt(0).toLowerCase() + lastName.toLowerCase();
        checkUsernameAvailability(baseUsername);
    }
}

// Verifica la disponibilidad del nombre de usuario generado
function checkUsernameAvailability(baseUsername) {
    fetch(`/check-username?base=${baseUsername}`)
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            const usernameInput = document.getElementById("username_add");
            usernameInput.value = data.username;
            usernameInput.disabled = false;
        })
        .catch((error) => console.error("Error:", error));
}

// Valida que la contraseña y su confirmación coincidan
function validatePassword() {
    const password = document.getElementById("password_add").value;
    const confirmPassword = document.getElementById(
        "password_confirmation_add"
    ).value;
    const passwordMatch = document.getElementById("passwordMatch");

    if (password !== confirmPassword) {
        passwordMatch.style.display = "block";
        return false;
    } else {
        passwordMatch.style.display = "none";
        return true;
    }
}

// Añade un evento para validar la contraseña antes de enviar el formulario
document
    .getElementById("addUserForm")
    .addEventListener("submit", function (event) {
        if (!validatePassword()) {
            event.preventDefault();
        }
    });

// Restablece la contraseña del usuario con una solicitud AJAX
function resetPassword(userId) {
    // Mostrar el modal de confirmación
    var confirmationModal = new bootstrap.Modal(
        document.getElementById("confirmationModal"),
        {
            backdrop: "static",
            keyboard: false,
        }
    );
    confirmationModal.show();

    // Asignar el evento de confirmación al botón del modal
    document.getElementById("confirmResetButton").onclick = function () {
        // Cerrar el modal de confirmación
        confirmationModal.hide();

        // Obtener el token CSRF del meta tag
        var csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        // Construir dinámicamente el ID del modal de edición
        var editModalId = "#editUserModal" + userId;

        // Realizar la solicitud AJAX para actualizar la contraseña y otros campos
        $.ajax({
            url: "/users/reset-password/" + userId,
            type: "POST",
            data: {
                _token: csrfToken, // Pasar el token CSRF
            },
            success: function (response) {
                // Cerrar modal de edición si es necesario
                $(editModalId).modal("hide");

                // Mostrar mensaje de éxito en un modal separado
                $("#successModal .modal-body").text(response.message);
                $("#successModal").modal({
                    backdrop: "static", // Evita que se cierre haciendo clic fuera del modal
                    keyboard: false, // Evita que se cierre al presionar la tecla ESC
                });
                $("#successModal").modal("show");
            },
            error: function (xhr, status, error) {
                // Mostrar mensaje de error en un modal
                $("#errorModal .modal-body").text(
                    "Hubo un error al restablecer la contraseña."
                );
                $("#errorModal").modal("show");
                console.error(error);
            },
        });
    };
}

// Evento click para el botón de restablecer contraseña
$(document).on("click", ".reset-password", function () {
    var userId = $(this).data("user-id");
    resetPassword(userId);
});

// Filtra los usuarios en la tabla según la búsqueda y el filtro de estado
function filterUsers() {
    const searchInput = document.getElementById('searchUser').value.toLowerCase().trim();
    const roleFilter = document.getElementById('roleFilter').value.toLowerCase().trim();
    const costCenterFilter = document.getElementById('costCenterFilter').value.toLowerCase().trim();
    const table = document.getElementById('dataTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // i = 1 para saltar el encabezado
        const usernameCell = rows[i].getElementsByTagName('td')[0]; // Columna de USUARIOS
        const employeeNameCell = rows[i].getElementsByTagName('td')[1]; // Columna de NOMBRE EMPLEADO
        const roleCell = rows[i].getElementsByTagName('td')[2]; // Columna de ROL
        const costCenterCell = rows[i].getElementsByTagName('td')[4]; // Columna de CENTRO DE COSTO

        if (usernameCell && employeeNameCell && roleCell && costCenterCell) {
            const username = usernameCell.textContent.toLowerCase().trim();
            const employeeName = employeeNameCell.textContent.toLowerCase().replace(/\s+/g, ' ').trim();
            const roles = roleCell.textContent.toLowerCase().trim();
            const costCenters = costCenterCell.textContent.toLowerCase().trim();

            const matchesName = username.includes(searchInput) || employeeName.includes(searchInput);
            const matchesRole = roleFilter === "" || roles.includes(roleFilter);
            const matchesCostCenter = costCenterFilter === "" || costCenters.includes(costCenterFilter);

            if (matchesName && matchesRole && matchesCostCenter) {
                rows[i].style.display = ""; // Mostrar la fila
            } else {
                rows[i].style.display = "none"; // Ocultar la fila
            }
        }
    }
}

function limpiarCampos() {
    $('#searchUser').val(''); // Limpiar el campo de búsqueda
}






