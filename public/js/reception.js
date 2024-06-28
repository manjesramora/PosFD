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

function updateRow(element) {
    let row = element.closest('tr');
    let cantidadRecibida = parseFloat(row.querySelector('.cantidad-recibida').value) || 0;
    let precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
    let subtotal = row.querySelector('.subtotal');
    subtotal.innerHTML = (cantidadRecibida * precioUnitario).toFixed(2) + "$";

    distributeFreight();
}

function distributeFreight() {
    let totalSubtotal = 0;
    let freightCost = parseFloat(document.getElementById('flete_input').value) || 0;

    // Calculate total subtotal
    document.querySelectorAll('#receptionTableBody tr').forEach(row => {
        let subtotal = parseFloat(row.querySelector('.subtotal').innerHTML.replace('$', '')) || 0;
        totalSubtotal += subtotal;
    });

    // Distribute freight cost
    document.querySelectorAll('#receptionTableBody tr').forEach(row => {
        let subtotal = parseFloat(row.querySelector('.subtotal').innerHTML.replace('$', '')) || 0;
        let freight = row.querySelector('.flete');
        let iva = row.querySelector('.iva');
        let freightAmount = ((subtotal / totalSubtotal) * freightCost).toFixed(2);
        freight.innerHTML = freightAmount + "$";
        iva.innerHTML = (subtotal * 0.16).toFixed(2) + "$"; // Assuming 16% IVA
    });

    // Update totals
    document.getElementById('totalSubtotal').innerHTML = totalSubtotal.toFixed(2) + "$";
    document.getElementById('totalFlete').innerHTML = freightCost.toFixed(2) + "$";
    updateTotalIva();
    updateTotalGeneral();
}

function updateTotalIva() {
    let totalIva = 0;

    document.querySelectorAll('#receptionTableBody tr').forEach(row => {
        let iva = parseFloat(row.querySelector('.iva').innerHTML.replace('$', '')) || 0;
        totalIva += iva;
    });

    document.getElementById('totalIva').innerHTML = totalIva.toFixed(2) + "$";
}

function updateTotalGeneral() {
    let totalSubtotal = parseFloat(document.getElementById('totalSubtotal').innerHTML.replace('$', '')) || 0;
    let totalFlete = parseFloat(document.getElementById('totalFlete').innerHTML.replace('$', '')) || 0;
    let totalIva = parseFloat(document.getElementById('totalIva').innerHTML.replace('$', '')) || 0;
    let totalGeneral = totalSubtotal + totalFlete + totalIva;

    document.getElementById('totalGeneral').innerHTML = totalGeneral.toFixed(2) + "$";
}

$(document).ready(function() {
    $('.numero-input').on('input', function() {
        var input = $(this).val();
        var optionsList = $(this).siblings('.autocomplete-list');
        optionsList.empty();

        $.ajax({
            url: '/autocomplete-numeros',
            method: 'GET',
            data: {
                input: input
            },
            success: function(response) {
                response.forEach(function(option) {
                    var listItem = $('<li></li>').text(option.CNCDIRID + ' - ' + option.CNCDIRNOM).attr('data-id', option.CNCDIRID).attr('data-name', option.CNCDIRNOM);
                    optionsList.append(listItem);
                });
            }
        });
    });

    $(document).on('click', '.autocomplete-list li', function() {
        var selectedValue = $(this).text();
        var input = $(this).closest('.input-group').find('.numero-input');
        input.val(selectedValue);
        input.siblings('.autocomplete-list').empty();
    });

    $(document).on('blur', '.numero-input', function() {
        setTimeout(function() {
            $('.autocomplete-list').empty();
        }, 200);
    });
});
// Autocomplete and clear input functions (unchanged)
$('#numero').on('input', function() {
    let query = $(this).val();

    if (query.length >= 3) {
        $.ajax({
            url: "/providers/autocomplete",
            type: "GET",
            data: {
                query: query,
                field: 'CNCDIRID'
            },
            success: function(data) {
                let dropdown = $('#numeroList');
                dropdown.empty().show();

                data.forEach(item => {
                    dropdown.append(`<li class="list-group-item" data-id="${item.CNCDIRID}" data-name="${item.CNCDIRNOM}">${item.CNCDIRID} - ${item.CNCDIRNOM}</li>`);
                });
            }
        });
    } else {
        $('#numeroList').hide();
    }
});

$(document).on('click', '#numeroList li', function() {
    let id = $(this).data('id');
    let name = $(this).data('name');
    $('#numero').val(id);
    $('#fletero').val(name); // Captura el nombre del fletero en el campo Fletero
    $('#numeroList').hide();
});

$('#clearNumero').on('click', function() {
    $('#numero').val('');
    $('#fletero').val(''); // Limpia también el campo Fletero
    $('#numeroList').hide();
});

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
    $('#numero').val(id); // Captura el número del proveedor en el campo Número
    $('#fleteroList').hide();
});

$('#clearFletero').on('click', function() {
    $('#fletero').val('');
    $('#numero').val(''); // Limpia también el campo Número
    $('#fleteroList').hide();
});
function validateCantidad(input) {
    const maxCantidad = parseFloat($(input).attr('max'));
    let cantidadRecibida = parseFloat($(input).val());
    if (cantidadRecibida > maxCantidad) {
        $(input).val(maxCantidad);
        cantidadRecibida = maxCantidad;
    }
    updateRow(input);
}

function validatePrecio(input) {
    const maxPrecio = parseFloat($(input).attr('max'));
    let precioUnitario = parseFloat($(input).val());
    if (precioUnitario > maxPrecio) {
        $(input).val(maxPrecio);
        precioUnitario = maxPrecio;
    }
    updateRow(input);
}

function updateRow(input) {
    const row = $(input).closest('tr');
    const cantidadRecibida = parseFloat(row.find('.cantidad-recibida').val()) || 0;
    const precioUnitario = parseFloat(row.find('.precio-unitario').val()) || 0;

    // Actualizar subtotal
    const subtotal = cantidadRecibida * precioUnitario;
    row.find('.subtotal').text(subtotal.toFixed(2) + '$');

    // Actualizar cálculos totales
    updateTotals();
}

function updateTotals() {
    let totalSubtotal = 0;
    let totalFlete = 0;
    let totalIva = 0;
    let totalGeneral = 0;

    $('#receptionTableBody tr').each(function() {
        const subtotal = parseFloat($(this).find('.subtotal').text()) || 0;
        const flete = parseFloat($(this).find('.flete').text()) || 0;
        const iva = parseFloat($(this).find('.iva').text()) || 0;

        totalSubtotal += subtotal;
        totalFlete += flete;
        totalIva += iva;
    });

    totalGeneral = totalSubtotal + totalFlete + totalIva;

    $('#totalSubtotal').text(totalSubtotal.toFixed(2) + '$');
    $('#totalFlete').text(totalFlete.toFixed(2) + '$');
    $('#totalIva').text(totalIva.toFixed(2) + '$');
    $('#totalGeneral').text(totalGeneral.toFixed(2) + '$');
}

function toggleFleteInput() {
    const fleteSelect = $('#flete_select').val();
    if (fleteSelect == 1) {
        $('#flete_input_div').show();
    } else {
        $('#flete_input_div').hide();
        $('#flete_input').val('');
        distributeFreight();
    }
}

function distributeFreight() {
    const fleteInput = parseFloat($('#flete_input').val()) || 0;
    let totalSubtotal = 0;

    $('#receptionTableBody tr').each(function() {
        const subtotal = parseFloat($(this).find('.subtotal').text()) || 0;
        totalSubtotal += subtotal;
    });

    $('#receptionTableBody tr').each(function() {
        const subtotal = parseFloat($(this).find('.subtotal').text()) || 0;
        const flete = (subtotal / totalSubtotal) * fleteInput;
        $(this).find('.flete').text(flete.toFixed(2) + '$');
    });

    updateTotals();
}

$(document).ready(function() {
    updateTotals();
});