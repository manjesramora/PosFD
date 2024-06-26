<!-- resources/views/orders.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Ordenes de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/sb-admin-2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('slidebar') <!-- Asegúrate de tener un archivo sidebar.blade.php en resources/views -->
        <div id="content-wrapper" class="d-flex flex-column dash" style="overflow-y: hidden;">
            <div id="content">
                @include('navbar') <!-- Asegúrate de tener un archivo navbar.blade.php en resources/views -->
                <div class="container-fluid">
                    <h1 class="mt-5" style="text-align: center;">ORDENES DE COMPRA</h1>
                    <br>
                    <!-- Formulario de filtro -->
                    <form method="GET" action="{{ route('orders') }}" class="mb-3" id="filterForm">
                        <!-- Campos del formulario -->
                        <div class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label for="ACMROIDOC" class="form-label">NO DE DOC:</label>
                                <input type="text" name="ACMROIDOC" id="ACMROIDOC" class="form-control" value="" inputmode="numeric">
                            </div>
                            <div class="col-md-2">
                                <label for="CNCDIRID" class="form-label">Proveedor ID:</label>
                                <input type="text" name="CNCDIRID" id="CNCDIRID" class="form-control" value="" inputmode="numeric">
                                <div id="idDropdown" class="dropdown-menu"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="CNCDIRNOM" class="form-label">Proveedor Nombre:</label>
                                <div class="input-group">
                                    <input type="text" name="CNCDIRNOM" id="CNCDIRNOM" class="form-control" value="">
                                    <div id="nameDropdown" class="dropdown-menu"></div>
                                    <button class="btn btn-danger" type="button" onclick="limpiarCampos()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Fecha de inicio:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label">Fecha de fin:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100" id="filterButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div id="errorMessage" style="color: red; display: none;"></div>
                    </form>
                    <!-- Tabla de órdenes -->
                    <div class="table-responsive">
                        <div class="container-fluid">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <div class="table-responsive small-font">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <!-- Añade aquí las columnas de tu tabla de órdenes -->
                                                    <th>NO. DOC</th>
                                                    <th>NO. PROV</th>
                                                    <th>PROVEDOR</th>
                                                    <th>FECHA DE ORDEN</th>
                                                    <th>PARTIDAS</th>
                                                    <th>ALMACEN</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($orders as $order)
                                                <tr>
                                                    <!-- Asegúrate de que $order->id sea el campo correcto -->
                                                    <td>{{ $order->ACMVOIDOC }}</td>
                                                    <td>{{ $order->CNCDIRID }}</td>
                                                    <td>{{ $order->provider->CNCDIRNOM }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($order->ACMVOIFDOC)->format('Y-m-d') }}</td>
                                                    <td>{{ $order->ACMVOIULIN }}</td>
                                                    <td>{{ $order->ACMVOIALID}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{ $orders->links() }} <!-- Paginación -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Coloca esto al inicio de tu archivo head -->
    

    <!-- Coloca esto al final del body -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/order.js') }}"></script>

</body>

</html>