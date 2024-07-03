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
                            <input type="number" id="flete_input" class="form-control" oninput="distributeFreight()">
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
                                                    <th class="col-md-1">LIN</th>
                                                    <th class="col-md-1">ID</th>
                                                    <th class="col-md-6">DESCRIPCION</th>
                                                    <th class="col-md-1">SKU</th>
                                                    <th class="col-md-1">UM</th>
                                                    <th class="col-md-1">Cantidad Solicitada</th>
                                                    <th class="col-md-1">Cantidad Recibida</th>
                                                    <th class="col-md-1">Precio Unitario</th>
                                                    <th class="col-md-1">IVA</th>
                                                    <th class="col-md-1">Subtotal</th>
                                                    <th class="col-md-1">Flete</th>
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
                                                        <td class="iva">{{ number_format($reception->ACMVOIIVA) }}%</td>
                                                        <td class="subtotal">0.00</td>
                                                        <td class="flete">0.00</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                       
                                            </table>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFleteInput() {
            var fleteSelect = document.getElementById('flete_select');
            var fleteInputDiv = document.getElementById('flete_input_div');
            if (fleteSelect.value == '1') {
                fleteInputDiv.style.display = 'block';
            } else {
                fleteInputDiv.style.display = 'none';
                document.getElementById('flete_input').value = '';
                distributeFreight();
            }
        }

        function limitCantidad(element) {
            var max = parseFloat(element.max);
            var value = parseFloat(element.value);
            if (value > max) {
                element.value = max;
            }
            calculateSubtotal(element.closest('tr'));
            distributeFreight();
        }

        function limitPrecio(element) {
            var max = parseFloat(element.max);
            var value = parseFloat(element.value);
            if (value > max) {
                element.value = max;
            }
            calculateSubtotal(element.closest('tr'));
            distributeFreight();
        }

        function calculateSubtotal(row) {
            var cantidadRecibida = parseFloat(row.querySelector('.cantidad-recibida').value) || 0;
            var precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
            var iva = parseFloat(row.querySelector('.iva').innerText.replace('%', '')) || 0;

            var subtotal = cantidadRecibida * precioUnitario;
            var ivaAmount = subtotal * iva / 100;
            row.querySelector('.subtotal').innerText = (subtotal + ivaAmount).toFixed(2);
        }

        function distributeFreight() {
            var flete = parseFloat(document.getElementById('flete_input').value) || 0;
            var table = document.getElementById('receptionTableBody');
            var rows = table.querySelectorAll('tr');
            var totalSubtotal = 0;
            
            rows.forEach(row => {
                var subtotal = parseFloat(row.querySelector('.subtotal').innerText) || 0;
                totalSubtotal += subtotal;
            });

            rows.forEach(row => {
                var subtotal = parseFloat(row.querySelector('.subtotal').innerText) || 0;
                var fleteProportion = (subtotal / totalSubtotal) * flete;
                row.querySelector('.flete').innerText = fleteProportion.toFixed(2);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.cantidad-recibida').forEach(element => {
                element.addEventListener('input', () => limitCantidad(element));
            });

            document.querySelectorAll('.precio-unitario').forEach(element => {
                element.addEventListener('input', () => limitPrecio(element));
            });
        });
    </script>
</body>
</html>
