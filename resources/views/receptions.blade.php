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
                                <button class="btn btn-danger btn-outline-ligth clear-input" type="button" id="clearNumero">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul id="numeroList" class="list-group" style="display: none;"></ul>
                        </div>
                        <div class="col-md-4">
                            <label for="fletero" class="form-label">Fletero:</label>
                            <div class="input-group">
                                <input type="text" id="fletero" class="form-control">
                                <button class="btn btn-danger btn-outline-ligth clear-input" type="button" id="clearFletero">
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
                        <!-- Other fields -->
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
                                                    <th class="col-md-1">Subtotal</th>
                                                    <th class="col-md-1">Flete</th>
                                                    <th class="col-md-1">IVA</th>
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
                                                        <input type="number" class="form-control cantidad-recibida" name="cantidad_recibida[]" value="" step="0.01" oninput="validateCantidad(this)" max="{{ $reception->ACMVOIQTO }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control precio-unitario" name="precio_unitario[]" value="{{ number_format($reception->ACMVOINPO, 2) }}" max="{{ $reception->ACMVOINPO }}" step="0.01" oninput="validatePrecio(this)">
                                                    </td>
                                                    <td class="subtotal">{{ number_format($reception->ACMVOIMRE, 2) }}$</td>
                                                    <td class="flete">0.00$</td>
                                                    <td class="iva">{{ number_format($reception->ACMVOITIVA, 2) }}$</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="8" class="text-end"><strong>Total</strong></td>
                                                    <td id="totalSubtotal">0.00$</td>
                                                    <td id="totalFlete">0.00$</td>
                                                    <td id="totalIva">0.00$</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="10" class="text-end"><strong>Total General</strong></td>
                                                    <td id="totalGeneral">0.00$</td>
                                                </tr>
                                            </tfoot>
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
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="assets/vendor/chart.js/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/reception.js') }}"></script>

</body>

</html>