// resources/js/users.js

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

document
    .getElementById("addUserForm")
    .addEventListener("submit", function (event) {
        if (!validatePassword()) {
            event.preventDefault();
        }
    });

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


function filterUsers() {
    const searchInput = document.getElementById('searchUser').value.toLowerCase().trim();
    const statusFilter = document.getElementById('statusFilter').value;
    const table = document.getElementById('dataTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // i = 1 para saltar el encabezado
        const employeeNameCell = rows[i].getElementsByTagName('td')[1]; // Columna de NOMBRE EMPLEADO
        const statusCell = rows[i].getElementsByTagName('td')[3]; // Columna de ESTADO

        if (employeeNameCell && statusCell) {
            const employeeName = employeeNameCell.textContent.toLowerCase().trim();
            const status = statusCell.textContent.trim();

            const matchesName = employeeName.includes(searchInput);
            const matchesStatus = statusFilter === "" || (statusFilter === "1" && status === "ACTIVO") || (statusFilter === "0" && status === "INACTIVO");

            if (matchesName && matchesStatus) {
                rows[i].style.display = ""; // Mostrar la fila
            } else {
                rows[i].style.display = "none"; // Ocultar la fila
            }
        }
    }
}