<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Detalles de Recepción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/sb-admin-2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body id="page-top">
    <div id="wrapper">
        @include('slidebar')
        <div id="content-wrapper" class="d-flex flex-column dash" style="overflow-y: hidden;">
            <div id="content">
                @include('navbar')
                <div class="container-fluid">
                    <h1 class="mt-5" style="text-align: center;">Detalles de Recepción</h1>
                    <br>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label for="numero" class="form-label">Número:</label>
                            <div class="input-group">
                                <input type="text" id="numero" class="form-control">
                                <button class="btn btn-danger btn-outline-light clear-input" type="button" id="clearNumero">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul id="numeroList" class="list-group" style="display: none;"></ul>
                        </div>
                        <div class="col-md-4">
                            <label for="fletero" class="form-label">Fletero:</label>
                            <div class="input-group">
                                <input type="text" id="fletero" class="form-control">
                                <button class="btn btn-danger btn-outline-light clear-input" type="button" id="clearFletero">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul id="fleteroList" class="list-group" style="display: none;"></ul>
                        </div>
                        <div class="col-md-1">
                            <label for="tipo_doc" class="form-label">Tipo Doc:</label>
                            <input type="text" id="tipo_doc" class="form-control" value="{{ $order->CNTDOCID }}" readonly>
                        </div>
                        <div class="col-md-1">
                            <label for="num_doc" class="form-label">No. de Doc:</label>
                            <input type="text" id="num_doc" class="form-control" value="{{ $order->ACMVOIDOC }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="nombre_proveedor" class="form-label">Nombre del Proveedor:</label>
                            <input type="text" id="nombre_proveedor" class="form-control" value="{{ $order->provider->CNCDIRNOM }}" readonly>
                        </div>
                        <div class="col-md-1">
                            <label for="referencia" class="form-label">Referencia:</label>
                            <select id="referencia" class="form-control">
                                <option value="1">FACTURA</option>
                                <option value="2">REMISION</option>
                                <option value="3">MISELANEO</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="almacen" class="form-label">Almacén:</label>
                            <input type="text" id="almacen" class="form-control" value="{{ $order->ACMVOIALID }}" readonly>
                        </div>
                        <div class="col-md-1">
                            <label for="ACMROIREF" class="form-label">Referencia:</label>
                            <input type="text" id="ACMROIREF" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="fecha" class="form-label">Fecha Recepcion:</label>
                            <input type="date" id="fecha" class="form-control" value="{{ $currentDate }}" readonly>
                        </div>
                        <div class="col-md-1">
                            <label for="rcn_final" class="form-label">DOC:</label>
                            <input type="text" id="rcn_final" class="form-control" value="RCN" readonly>
                        </div>
                        <div class="col-md-1">
                            <label for="num_rcn_letras" class="form-label">NO DE DOC:</label>
                            <input type="text" id="num_rcn_letras" class="form-control" value="{{ $num_rcn_letras }}" readonly>
                        </div>
                        <div class="col-md-1">
                            <label for="flete_select" class="form-label">¿Hay flete?</label>
                            <select id="flete_select" class="form-control" onchange="toggleFleteInput()">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="col-md-1" id="flete_input_div" style="display: none;">
                            <label for="flete_input" class="form-label">Flete:</label>
                            <input type="number" id="flete_input" class="form-control" oninput="distributeFreight()" step="0.01" min="0">
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('orders') }}" class="btn btn-secondary me-2">Volver a Órdenes</a>
                            <a href="#" class="btn btn-warning">Recepcionar</a>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <div class="container-fluid">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <div class="table-responsive small-font">
                                        <table class="table table-bordered table-centered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-0">LIN</th>
                                                    <th class="col-md-0">ID</th>
                                                    <th class="col-md-6">DESCRIPCION</th>
                                                    <th class="col-md-0">SKU</th>
                                                    <th class="col-md-0">UM</th>
                                                    <th class="col-md-0">Cantidad <br> Solicitada</th>
                                                    <th class="col-md-1">Cantidad <br> Recibida</th>
                                                    <th class="col-md-0">Precio Unitario</th>
                                                    <th class="col-md-0">IVA</th>
                                                    <th class="col-md-1">Subtotal</th>
                                                    <th class="col-md-1">Flete</th>
                                                    <th class="col-md-1">Porcentaje <br> de Flete</th>
                                                </tr>
                                            </thead>
                                            <tbody id="receptionTableBody">
                                                @foreach ($receptions as $reception)
                                                    <tr>
                                                        <td>{{ number_format($reception->ACMVOILIN) }}</td>
                                                        <td>{{ $reception->ACMVOIPRID }}</td>
                                                        <td>{{ $reception->ACMVOIPRDS }}</td>
                                                        <td>{{ $reception->ACMVOINPAR }}</td>
                                                        <td>{{ $reception->ACMVOIUMT }}</td>
                                                        <td>{{ number_format($reception->ACMVOIQTO, 2) }}</td>
                                                        <td>
                                                            <input type="number" class="form-control cantidad-recibida" name="cantidad_recibida[]" value="" step="0.01" min="0" max="{{ $reception->ACMVOIQTO }}" oninput="limitCantidad(this)">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control precio-unitario" name="precio_unitario[]" value="{{ number_format($reception->ACMVOINPO, 2) }}" min="0" max="{{ number_format($reception->ACMVOINPO, 2) }}" step="0.01" oninput="limitPrecio(this)">
                                                        </td>
                                                        <td>{{ number_format($reception->ACMVOIIVA, 2) }}</td>
                                                        <td class="subtotal"></td>
                                                        <td class="flete"></td>
                                                        <td class="porcentaje-flete"></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <br>
                                        <button id="saveButton" class="btn btn-primary" onclick="saveData()">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
    <script>
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


        function toggleFleteInput() {
            const fleteSelect = document.getElementById('flete_select');
            const fleteInputDiv = document.getElementById('flete_input_div');
            if (fleteSelect.value == '1') {
                fleteInputDiv.style.display = 'block';
            } else {
                fleteInputDiv.style.display = 'none';
                distributeFreight();
            }
        }

        function distributeFreight() {
            const fleteInput = document.getElementById('flete_input').value;
            const rows = document.querySelectorAll('#receptionTableBody tr');
            let totalSubtotal = 0;

            rows.forEach(row => {
                const subtotalCell = row.querySelector('.subtotal');
                const cantidadRecibida = parseFloat(row.querySelector('.cantidad-recibida').value) || 0;
                const precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
                const subtotal = cantidadRecibida * precioUnitario;
                subtotalCell.textContent = subtotal.toFixed(2);
                totalSubtotal += subtotal;
            });

            rows.forEach(row => {
                const subtotalCell = row.querySelector('.subtotal').textContent;
                const subtotal = parseFloat(subtotalCell);
                const porcentajeFlete = totalSubtotal > 0 ? (subtotal / totalSubtotal) * 100 : 0;
                const flete = (fleteInput * porcentajeFlete / 100).toFixed(2);
                row.querySelector('.flete').textContent = flete;
                row.querySelector('.porcentaje-flete').textContent = porcentajeFlete.toFixed(2) + '%';
            });
        }

        function limitCantidad(input) {
            const maxCantidad = parseFloat(input.max);
            if (parseFloat(input.value) > maxCantidad) {
                input.value = maxCantidad;
            }
        }

        function limitPrecio(input) {
            const maxPrecio = parseFloat(input.max);
            if (parseFloat(input.value) > maxPrecio) {
                input.value = maxPrecio;
            }
        }

        function saveData() {
            const data = {
                numero: $('#numero').val(),
                fletero: $('#fletero').val(),
                tipo_doc: $('#tipo_doc').val(),
                num_doc: $('#num_doc').val(),
                nombre_proveedor: $('#nombre_proveedor').val(),
                referencia: $('#referencia').val(),
                almacen: $('#almacen').val(),
                ACMROIREF: $('#ACMROIREF').val(),
                fecha: $('#fecha').val(),
                rcn_final: $('#rcn_final').val(),
                num_rcn_letras: $('#num_rcn_letras').val(),
                flete: $('#flete_input').val(),
                items: []
            };

            $('#receptionTableBody tr').each(function() {
                const row = $(this);
                data.items.push({
                    cantidad_recibida: row.find('.cantidad-recibida').val(),
                    precio_unitario: row.find('.precio-unitario').val()
                });
            });

            $.ajax({
                url: '/save-data',
                type: 'POST',
                data: data,
                success: function(response) {
                    alert('Datos guardados exitosamente');
                },
                error: function(xhr) {
                    alert('Hubo un error al guardar los datos');
                }
            });
        }
    </script>
</body>
</html>

