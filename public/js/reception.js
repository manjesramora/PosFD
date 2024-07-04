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


function distributeFreight() {
    const fleteInput = document.getElementById('flete_input');
    const fleteValue = parseFloat(fleteInput.value) || 0;

    const rows = document.querySelectorAll('#receptionTableBody tr');
    let totalSubtotal = 0;
    let totalFlete = 0;
    let totalPorcentajeFlete = 0;
    let subtotalArray = [];

    // Primer recorrido: calcular subtotales
    rows.forEach(row => {
        const cantidadRecibida = parseFloat(row.querySelector('.cantidad-recibida').value) || 0;
        const precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
        const subtotal = cantidadRecibida * precioUnitario;
        subtotalArray.push(subtotal);
        totalSubtotal += subtotal;
        row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
    });

    // Segundo recorrido: calcular flete y porcentaje de flete
    rows.forEach((row, index) => {
        const subtotal = subtotalArray[index];
        const fleteCell = row.querySelector('.flete');
        const porcentajeFleteCell = row.querySelector('.porcentaje-flete');

        let fleteProporcion = 0;
        let porcentajeFlete = 0;

        if (totalSubtotal > 0) {
            fleteProporcion = (subtotal / totalSubtotal) * fleteValue;
            porcentajeFlete = (subtotal / totalSubtotal) * 100;
        }

        fleteCell.textContent = fleteProporcion.toFixed(2);
        porcentajeFleteCell.textContent = porcentajeFlete.toFixed(2) + '%';

        totalFlete += fleteProporcion;
        totalPorcentajeFlete += porcentajeFlete;
    });

    // Actualizar totales
    document.getElementById('totalSubtotal').textContent = totalSubtotal.toFixed(2);
    document.getElementById('totalFlete').textContent = totalFlete.toFixed(2);
    document.getElementById('totalPorcentajeFlete').textContent = totalPorcentajeFlete.toFixed(2) + '%';
}

document.getElementById('flete_input').addEventListener('input', distributeFreight);
document.querySelectorAll('.cantidad-recibida').forEach(input => input.addEventListener('input', distributeFreight));
document.querySelectorAll('.precio-unitario').forEach(input => input.addEventListener('input', distributeFreight));

function limitCantidad(input) {
    const max = parseFloat(input.getAttribute('max'));
    const value = parseFloat(input.value);
    if (value > max) {
        input.value = max;
    }
    distributeFreight();
}

function limitPrecio(input) {
    const max = parseFloat(input.getAttribute('max'));
    const value = parseFloat(input.value);
    if (value > max) {
        input.value = max;
    }
    distributeFreight();
}
