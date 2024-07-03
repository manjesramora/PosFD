function toggleFleteInput() {
    var selectBox = document.getElementById("flete_select");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    var fleteInputDiv = document.getElementById("flete_input_div");

    if (selectedValue === "1") {
        fleteInputDiv.style.display = "block";
    } else {
        fleteInputDiv.style.display = "none";
        document.getElementById("flete_input").value = "";
        distributeFreight();
    }
}

// Autocompletado para el campo Número
$(document).on('click', '#numeroList li', function() {
    let id = $(this).data('id');
    let name = $(this).data('name');
    $('#numero').val(id);
    $('#fletero').val(name);
    $('#numeroList').hide();
});

$('#clearNumero').on('click', function() {
    $('#numero').val('');
    $('#fletero').val('');
    $('#numeroList').hide();
});

// Autocompletado para el campo Fletero
$('#fletero').on('input', function() {
    let query = $(this).val();

    if (query.length >= 3) {
        $.ajax({
            url: "/providers/autocomplete",
            type: "GET",
            data: {
                query: query,
                field: 'CNCDIRNOM'
            },
            success: function(data) {
                let dropdown = $('#fleteroList');
                dropdown.empty().show();

                data.forEach(item => {
                    dropdown.append(`<li class="list-group-item" data-id="${item.CNCDIRID}" data-name="${item.CNCDIRNOM}">${item.CNCDIRID} - ${item.CNCDIRNOM}</li>`);
                });
            }
        });
    } else {
        $('#fleteroList').hide();
    }
});

$(document).on('click', '#fleteroList li', function() {
    let id = $(this).data('id');
    let name = $(this).data('name');
    $('#fletero').val(name);
    $('#numero').val(id);
    $('#fleteroList').hide();
});

$('#clearFletero').on('click', function() {
    $('#fletero').val('');
    $('#numero').val('');
    $('#fleteroList').hide();
});

// Llamar a updateTotals() solo si está definida
if (typeof updateTotals === 'function') {
    updateTotals();
}
function validateCantidad(input) {
    let maxQuantity = parseFloat(input.max) || 0;
    let minQuantity = parseFloat(input.min) || 0;
    let value = parseFloat(input.value) || 0;

    if (value > maxQuantity) {
        input.value = maxQuantity;
    } else if (value < minQuantity) {
        input.value = minQuantity;
    }

    // Asegúrate de que el campo tenga al menos 0
    if (isNaN(input.value) || input.value === '') {
        input.value = minQuantity;
    }

    updateRow(input); // Llamar a updateRow para recalcular totales
}

// Función para validar el precio unitario
function validatePrecio(input) {
    let maxPrice = parseFloat(input.max) || 0;
    let minPrice = parseFloat(input.min) || 0;
    let value = parseFloat(input.value) || 0;

    if (value > maxPrice) {
        input.value = maxPrice;
    } else if (value < minPrice) {
        input.value = minPrice;
    }

    // Asegúrate de que el campo tenga al menos 0
    if (isNaN(input.value) || input.value === '') {
        input.value = minPrice;
    }

    updateRow(input); // Llamar a updateRow para recalcular totales
}

