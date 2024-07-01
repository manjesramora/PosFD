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
    let ivaPorcentaje = parseFloat(row.querySelector('.iva-porcentaje').textContent) || 0; // Obtener porcentaje de IVA
    let subtotal = row.querySelector('.subtotal');
    subtotal.innerHTML = (cantidadRecibida * precioUnitario).toFixed(2) + "$";

    // Recalculando IVA basado en el porcentaje del 16% de ACMVOITIVA
    let iva = row.querySelector('.iva');
    let ivaAmount = (subtotal.textContent.replace('$', '') * (ivaPorcentaje / 100)).toFixed(2);
    iva.innerHTML = ivaAmount + "$";

    distributeFreight();
}

function distributeFreight() {
    let totalSubtotal = 0;
    let freightCost = parseFloat(document.getElementById('flete_input').value) || 0;

    // Calcular total subtotal
    document.querySelectorAll('#receptionTableBody tr').forEach(row => {
        let subtotal = parseFloat(row.querySelector('.subtotal').innerHTML.replace('$', '')) || 0;
        totalSubtotal += subtotal;
    });

    // Distribuir costo del flete
    document.querySelectorAll('#receptionTableBody tr').forEach(row => {
        let subtotal = parseFloat(row.querySelector('.subtotal').innerHTML.replace('$', '')) || 0;
        let freight = row.querySelector('.flete');
        let iva = row.querySelector('.iva');
        let ivaPorcentaje = parseFloat(row.querySelector('.iva-porcentaje').textContent) || 0; // Obtener porcentaje de IVA
        let freightAmount = ((subtotal / totalSubtotal) * freightCost).toFixed(2);
        freight.innerHTML = freightAmount + "$";

        // Recalculando IVA basado en el porcentaje de ACMVOITIVA
        let ivaAmount = (subtotal * (ivaPorcentaje / 100)).toFixed(2);
        iva.innerHTML = ivaAmount + "$";
    });

    // Actualizar totales
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
    // Actualización de la tabla al cambiar la cantidad o el precio
    $(document).on('input', '.cantidad-recibida, .precio-unitario', function() {
        updateRow(this);
    });

    $('#flete_select').change(function() {
        toggleFleteInput();
    });

    $('#flete_input').on('input', function() {
        distributeFreight();
    });

    // Autocompletado para el campo Número
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
        $('#numero').val(id); // Captura el número del proveedor en el campo Número
        $('#fleteroList').hide();
    });

    $('#clearFletero').on('click', function() {
        $('#fletero').val('');
        $('#numero').val(''); // Limpia también el campo Número
        $('#fleteroList').hide();
    });

    // Inicializar la tabla al cargar la página
    updateTotals();
});

function toggleFleteInput() {
    var fleteSelect = document.getElementById('flete_select');
    var fleteInputDiv = document.getElementById('flete_input_div');
    if (fleteSelect.value == "1") {
        fleteInputDiv.style.display = 'block';
    } else {
        fleteInputDiv.style.display = 'none';
        document.getElementById('flete_input').value = '';
        distributeFreight();
    }
}

function distributeFreight() {
    // Your logic to distribute freight cost among products
}

function validateCantidad(input) {
    // Your logic to validate received quantity
}

function validatePrecio(input) {
    // Your logic to validate unit price
}