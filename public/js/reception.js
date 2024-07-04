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
     
    }
});

$(document).on('click', '#fleteroList li', function() {
    let id = $(this).data('id');
    let name = $(this).data('name');
    $('#fletero').val(name);
    $('#numero').val(id);
});

$('#clearFletero').on('click', function() {
    $('#fletero').val('');
    $('#numero').val('');

});

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
