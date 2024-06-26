document.addEventListener("DOMContentLoaded", function() {

    // Función para validar entradas de texto en tiempo real para campos sin números
    function validateTextInput(event) {
        const input = event.target;
        const errorSpan = input.nextElementSibling;

        // Permitir solo letras y espacios, sin números
        const valid = /^[a-zA-Z\s]*$/.test(input.value);
        if (!valid) {
            errorSpan.textContent = 'Solo se permiten letras y espacios.';
            input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
        } else {
            errorSpan.textContent = '';
        }
    }

    // Función para validar entradas de texto en tiempo real para campos solo con números
    function validateNumberInput(event) {
        const input = event.target;
        const errorSpan = input.nextElementSibling;

        // Permitir solo números
        const valid = /^[0-9]*$/.test(input.value);
        if (!valid) {
            errorSpan.textContent = 'Solo se permiten números.';
            input.value = input.value.replace(/[^0-9]/g, '');
        } else {
            errorSpan.textContent = '';
        }
    }

    // Función para limpiar el mensaje de error al dejar de interactuar con el campo
    function clearErrorMessage(event) {
        const input = event.target;
        const errorSpan = input.nextElementSibling;
        errorSpan.textContent = '';
    }

    // Aplicar validación en tiempo real solo a first_name, last_name y middle_name del modal de agregar y editar
    const nameFields = ['first_name', 'last_name', 'middle_name'];
    nameFields.forEach(function (fieldId) {
        document.querySelectorAll(`#${fieldId}, [id^="edit_${fieldId}"]`).forEach(function (element) {
            element.addEventListener('input', validateTextInput);
            element.addEventListener('blur', clearErrorMessage);
        });
    });

    // Aplicar validación en tiempo real solo a postal_code, phone y phone2 del modal de agregar y editar
    const numberFields = ['postal_code', 'phone', 'phone2'];
    numberFields.forEach(function (fieldId) {
        document.querySelectorAll(`#${fieldId}, [id^="edit_${fieldId}"]`).forEach(function (element) {
            element.addEventListener('input', validateNumberInput);
            element.addEventListener('blur', clearErrorMessage);
        });
    });

    // Validación al enviar el formulario de agregar empleado
    document.getElementById('addEmployeeForm').addEventListener('submit', function (event) {
        validateForm(event, 'addEmployeeForm');
    });

    // Validación al enviar los formularios de editar empleado
    document.querySelectorAll('form[id^="editEmployeeForm"]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            validateForm(event, form.id);
        });
    });

    function validateForm(event, formId) {
        const fields = ['first_name', 'last_name', 'middle_name', 'curp', 'rfc', 'colony', 'street', 'external_number', 'internal_number', 'postal_code', 'phone', 'phone2', 'birth'];
        let validForm = true;

        fields.forEach(function (field) {
            const input = document.querySelector(`#${formId} [name="${field}"]`);
            if (!input) {
                console.warn(`Input with name "${field}" not found in form "${formId}"`);
                return;
            }
            const errorSpan = input.nextElementSibling;
            if (!errorSpan) {
                console.warn(`Error span not found for input "${field}" in form "${formId}"`);
                return;
            }

            errorSpan.textContent = '';

            if (!input.checkValidity()) {
                if (input.validity.patternMismatch) {
                    console.log(`Pattern mismatch for field: ${field}`);
                    if (field === 'postal_code') {
                        errorSpan.textContent = 'El código postal debe consistir de 5 dígitos.';
                    } else if (field === 'phone' || field === 'phone2') {
                        errorSpan.textContent = 'El teléfono debe consistir de 10 dígitos.';
                    } else if (field === 'curp') {
                        errorSpan.textContent = 'El CURP debe tener el formato AAAA000000HAAAAA00';
                    } else if (field === 'rfc') {
                        errorSpan.textContent = 'El RFC debe tener el formato AAAA000000AAA';
                    } else {
                        errorSpan.textContent = 'Este campo tiene un formato incorrecto.';
                    }
                } else if (input.validity.valueMissing) {
                    console.log(`Value missing for field: ${field}`);
                    errorSpan.textContent = 'Este campo es obligatorio.';
                } else if (input.validity.tooLong) {
                    console.log(`Too long input for field: ${field}`);
                    errorSpan.textContent = 'Este campo no puede tener más de ' + input.maxLength + ' caracteres.';
                }
                validForm = false;
            }
        });

        if (!validForm) {
            event.preventDefault();
        }
    }
});

function filterEmployees() {
    const searchInput = document.getElementById('searchEmployee').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const table = document.getElementById('dataTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // i = 1 para saltar el encabezado
        const nameCell = rows[i].getElementsByTagName('td')[0]; // Columna de NOMBRE(S)
        const lastNameCell = rows[i].getElementsByTagName('td')[1]; // Columna de APELLIDO PATERNO
        const middleNameCell = rows[i].getElementsByTagName('td')[2]; // Columna de APELLIDO MATERNO
        const statusCell = rows[i].getElementsByTagName('td')[13]; // Columna de ESTADO

        if (nameCell && lastNameCell && middleNameCell && statusCell) {
            const name = nameCell.textContent.toLowerCase();
            const lastName = lastNameCell.textContent.toLowerCase();
            const middleName = middleNameCell.textContent.toLowerCase();
            const status = statusCell.textContent.trim().toLowerCase();

            const fullName = `${name} ${lastName} ${middleName}`;

            const matchesName = fullName.includes(searchInput);
            const matchesStatus = statusFilter === "" || (statusFilter === "1" && status === "activo") || (statusFilter === "0" && status === "inactivo");

            if (matchesName && matchesStatus) {
                rows[i].style.display = ""; // Mostrar la fila
            } else {
                rows[i].style.display = "none"; // Ocultar la fila
            }
        }
    }
}

$(document).ready(function() {
    // Evento de clic en el encabezado de la columna para ordenar
    $("th.sortable").click(function () {
        var columnIndex = $(this).index();
        var asc = $(this).hasClass("asc");
        var desc = $(this).hasClass("desc");

        // Si la columna ya está ordenada en orden ascendente, ordenar descendentemente
        if (asc) {
            $("th").removeClass("asc").removeClass("desc");
            $(this).addClass("desc");
            // Mostrar indicador de ordenamiento en el encabezado de la columna
            $(this).find(".sort-indicator").remove();
            $(this).append('<span class="sort-indicator">&#x25BE;</span>'); // Flecha hacia abajo
            sortTable(columnIndex, false);
        }
        // Si la columna ya está ordenada en orden descendente, quitar el ordenamiento
        else if (desc) {
            $("th").removeClass("asc").removeClass("desc");
            $(this).find(".sort-indicator").remove();
            sortTable(0, true); // Orden predeterminado (primera columna ascendente)
        }
        // Si la columna no está ordenada, ordenar ascendente
        else {
            $("th").removeClass("asc").removeClass("desc");
            $(this).addClass("asc");
            // Mostrar indicador de ordenamiento en el encabezado de la columna
            $(this).find(".sort-indicator").remove();
            $(this).append('<span class="sort-indicator">&#x25B4;</span>'); // Flecha hacia arriba
            sortTable(columnIndex, true);
        }
    });
});

function sortTable(columnIndex, asc) {
    var table = document.getElementById("dataTable");
    var rows = table.rows;
    var switching = true;
    var shouldSwitch;
    var i;
    var x, y;

    while (switching) {
        switching = false;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[columnIndex];
            y = rows[i + 1].getElementsByTagName("TD")[columnIndex];

            if (asc) {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            } else {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
}


