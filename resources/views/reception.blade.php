<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Trabajando con Ordenes de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/sb-admin-2.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('slidebar')
        <div id="content-wrapper" class="d-flex flex-column dash" style="overflow-y: hidden;">
            <div id="content">
                @include('navbar')
                <div class="container-fluid">
                    <h1 class="mt-5" style="text-align: center;">RECEPCION</h1>
                    <br>
                    <div class="row justify-content-center">
                        <!-- Nuevos Inputs -->
                        <div class="col-md-2 position-relative">
                            <label for="numero" class="form-label">Número:</label>
                            <div class="input-group">
                                <input type="text" id="numero" class="form-control">
                                <button class="btn btn-outline-secondary clear-input" type="button" id="clearNumero">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul id="numeroFleteList" class="list-group" style="display: none;"></ul>
                        </div>
                        <div class="col-md-3">
                            <label for="fletero" class="form-label">Fletero:</label>
                            <div class="input-group">
                                <input type="text" id="fletero" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary clear-input" type="button" id="clearFletero">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <ul id="fleteroList" class="list-group" style="display: none;"></ul>
                        </div>
                        <!-- Combo Referencia -->
                        @if ($orders->isNotEmpty())
                        <!-- Fila 1 -->
                        <div class="col-md-1">
                            <label for="tipo_doc" class="form-label">Tipo Doc:</label>
                            <input type="text" id="tipo_doc" class="form-control" value="{{ $orders->first()->ACMROITDOC }}" disabled>
                        </div>
                        <!-- Combo Referencia -->
                        <div class="col-md-1">
                            <label for="num_doc" class="form-label">No. de Doc:</label>
                            <input type="text" id="num_doc" class="form-control" value="{{ $nextNumber }}" disabled>
                        </div>
                        <div class="col-md-1">
                            <label for="almacen" class="form-label">Almacén:</label>
                            <input type="text" id="almacen" class="form-control" value="{{ $orders->first()->INALMNID }}" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="nombre_proveedor" class="form-label">Nombre del Proveedor:</label>
                            <input type="text" id="nombre_proveedor" class="form-control" value="{{ $orders->first()->provider->CNCDIRNOM ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-2">
                            <label for="referencia" class="form-label">Referencia:</label>
                            <select id="referencia" class="form-control">
                                <option value="1">FACTURA</option>
                                <option value="2">REMISION</option>
                                <option value="3">MISELANEO</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="ACMROIREF" class="form-label">Referencia:</label>
                            <input type="text" name="ACMROIREF" id="ACMROIREF" class="form-control" value="{{ request('ACMROIREF') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="fecha" class="form-label">Fecha:</label>
                            <input type="text" id="fecha" class="form-control" value="{{ \Illuminate\Support\Carbon::parse($orders->first()->ACMROIFDOC)->format('Y-m-d') }}" disabled>
                        </div>
                        <!-- Fila 2 -->
                        <div class="col-md-1">
                            <label for="flete" class="form-label">Flete:</label>
                            <select id="flete" class="form-control">
                                <option value="no">No</option>
                                <option value="si">Sí</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-none" id="flete-input-container">
                            <label for="flete_valor" class="form-label">Valor del Flete:</label>
                            <input type="number" id="flete_valor" class="form-control" value="">
                        </div>
                        <br>
                        <div class="row">
                            <div class="mt-3 d-flex justify-content-center">
                                <form id="recepcionarForm" action="{{ route('order.recepcionar') }}" method="POST">
                                    @csrf
                                    <button type="submit" id="recepcionar" class="btn btn-primary btn-sm">
                                        <i class="fas fa-truck mr-2"></i>Recepcionar
                                    </button>
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="col-md-12 text-center">
                            <p>No se encontraron datos.</p>
                        </div>
                        @endif
                    </div>
                </div>
                <br>
                <!-- Order Table -->
                <div class="table-responsive">
                    <div class="container-fluid">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="table-responsive small-font">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th class="col-1 text-center align-middle">NO</th>
                                                <th class="col-1 text-center align-middle">DESC</th>
                                                <th class="col-1 text-center align-middle">EMP</th>
                                                <th class="col-1 text-center align-middle">SKU</th>
                                                <th class="col-1 text-center align-middle">CODIGO</th>
                                                <th class="col-1 text-center align-middle">UM</th>
                                                <th class="col-1 text-center align-middle">PESO (KG)</th>
                                                <th class="col-1 text-center align-middle">VOL (MT3)</th>
                                                <th class="col-1 text-center align-middle">CANT. SOLICITADA</th>
                                                <th class="col-1 text-center align-middle">CANTIDAD REAL</th>
                                                <th class="col-1 text-center align-middle">PRECIO UNITARIO</th>
                                                <th class="col-1 text-center align-middle">IMPORTE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                            @if ($order->ACMROICXP == 'S')
                                            <tr>
                                                <td class="col-1 text-center align-middle">{{ intval($order->ACMROILIN) }}</td>
                                                <td class="col-1 text-center align-middle">{{ $order->ACMROIDSC }}</td>
                                                <td class="col-1 text-center align-middle">{{ $order->ACMROIUMT }}</td>
                                                <td class="col-1 text-center align-middle inprodid">{{ $order->product->INPRODI2 }}</td>
                                                <td class="col-1 text-center align-middle">{{ $order->product->INPRODCBR }}</td>
                                                <td class="col-1 text-center align-middle">{{ $order->ACMROIUMT }}</td>
                                                
                                                <td class="col-1 text-center align-middle">{{ number_format($order->ACMROIPESOU, 2) }}</td>
                                                <td class="col-1 text-center align-middle">{{ number_format($order->ACMROIVOLU, 2) }}</td>
                                                <td class="col-1 text-center align-middle">{{ number_format($order->ACMROIQTTR, 2) }}</td>
                                                <td class="col-1 text-center align-middle">
                                                    <input type="number" class="form-control acmroiqt" data-cantidad-recibir="{{ $order->ACMROIQTTR }}" value="{{ number_format($order->ACMROIQT, 2) }}">
                                                </td>
                                                <td class="col-1 text-center align-middle">
                                                    <div class="currency-input-container">
                                                        <input type="text" class="form-control acmroinp" value="{{ number_format($order->ACMROINP, 2) }}">
                                                    </div>
                                                </td>
                                                <td class="col-1 text-center align-middle"> {{ number_format($order->ACMROING, 2) }}</td>
                                            </tr>
                                            @endif
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
        <script src="assets/vendor/jquery/jquery.min.js"></script>
        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="assets/vendor/chart.js/Chart.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('js/reception.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <button id="scrollToTopBtn" title="Regresar Arriba"><i class="fas fa-arrow-up"></i></button>
        <script>
            $(document).ready(function() {
                $('#flete').change(function() {
                    var valorFlete = $('#flete').val();
                    if (valorFlete === 'si') {
                        $('#flete-input-container').removeClass('d-none');
                    } else {
                        $('#flete-input-container').addClass('d-none');
                        $('#flete_valor').val('');
                    }
                });
            });
        </script>
</body>

</html>