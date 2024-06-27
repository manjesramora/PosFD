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
                    <div class="table-responsive">
                        <div class="container-fluid">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <div class="table-responsive small-font">
                                        <table class="table table-bordered table-centered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-1">Linea</th>
                                                    <th class="col-md-1">Producto ID</th>
                                                    <th class="col-md-2">Descripción</th>
                                                    <th class="col-md-1">SKU</th>
                                                    <th class="col-md-1">Unidad de Medida</th>
                                                    <th class="col-md-1">Cantidad</th>
                                                    <th class="col-md-1">Precio Unit</th>
                                                    <th class="col-md-1">subtotal</th>
                                                    <th class="col-md-1">Iva</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($receptions as $reception)
                                                <tr>
                                                    <td>{{ number_format($reception->ACMVOILIN) }}</td>
                                                    <td>{{ $reception->ACMVOIPRID }}</td>
                                                    <td>{{ $reception->ACMVOIPRDS }}</td>
                                                    <td>{{ $reception->ACMVOINPAR, 2 }}</td>
                                                    <td>{{ $reception->ACMVOIUMT }}</td>
                                                    <td>{{ number_format($reception->ACMVOIQTO, 2) }}</td>
                                                    <td>{{ number_format($reception->ACMVOINPO,2) }}$</td>
                                                    <td>{{ number_format($reception->ACMVOIMRE, 2) }}$</td>
                                                    <td>{{ number_format($reception->ACMVOINIO),2 }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('orders') }}" class="btn btn-secondary">Volver a Órdenes</a>
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
    <script src="{{ asset('js/order.js') }}"></script>
</body>

</html>
