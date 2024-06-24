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
                    <h1 class="mt-5" style="text-align: center;">ORDENES DE COMPRA</h1>
                    <br>
                    <!-- Formulario de filtro -->
                    <form method="GET" action="{{ route('orders.search') }}" class="mb-3" id="filterForm">
                        <!-- Campos del formulario -->
                        <div class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label for="ACMROIDOC" class="form-label">NO DE DOC:</label>
                                <input type="text" name="ACMROIDOC" id="ACMROIDOC" class="form-control" value="{{ request('ACMROIDOC') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-2">
                                <label for="CNCDIRID" class="form-label">Proveedor ID:</label>
                                <input type="text" name="CNCDIRID" id="CNCDIRID" class="form-control" value="{{ request('CNCDIRID') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div id="idDropdown" class="dropdown-menu"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="CNCDIRNOM" class="form-label">Proveedor Nombre:</label>
                                <div class="input-group">
                                    <input type="text" name="CNCDIRNOM" id="CNCDIRNOM" class="form-control" value="{{ request('CNCDIRNOM') }}">
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
                                                    <th class="col-1 text-center align-middle">TIPO DE DOC.</th>
                                                    <th class="col-1 text-center align-middle">NUM. DE DOC.</th>
                                                    <th class="col-1 text-center align-middle">FECHA</th>
                                                    <th class="col-1 text-center align-middle">ALMACEN</th>
                                                    <th class="col-1 text-center align-middle">NOMBRE DE PROVEDOR</th>
                                                    <th class="col-1 text-center align-middle">ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($orders)
                                                @foreach ($orders as $order)
                                                @if (isset($order->ACMROICXP) && $order->ACMROICXP !== 'S')
                                                <tr>
                                                    <td class="col-1 text-center align-middle">{{ $order->CNTDOCID }}</td>
                                                    <td class="col-1 text-center align-middle">{{ $order->ACMROIDOC }}</td>
                                                    <td class="col-1 text-center align-middle">{{ \Carbon\Carbon::parse($order->ACMROIFDOC)->format('Y-m-d') }}</td>
                                                    <td class="col-1 text-center align-middle">{{ $order->INALMNID }}</td>
                                                    <td class="col-1 text-center align-middle">{{ $providers[$order->CNCDIRID] ?? 'N/A' }}</td>
                                                    <td class="col-1 text-center align-middle">
                                                        <!-- Botón de recepcionar mercancía -->
                                                        <a href="{{ route('reception', ['ACMROIDOC' => $order->ACMROIDOC]) }}" class="btn btn-primary">
                                                            <i class="fas fa-truck"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endif
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="6" class="text-center">No se encontraron órdenes de compra.</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="mt-3 mx-auto" style="width: fit-content;">
                                    @if ($orders)
                                    {{ $orders->withQueryString()->links() }}
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <br><br>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="proveedoresModal" tabindex="-1" aria-labelledby="proveedoresModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="proveedoresModalLabel">Seleccionar Proveedor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <input type="text" id="searchProvider" class="form-control" placeholder="Buscar por ID o Nombre del Proveedor">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Seleccionar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="proveedoresTable">
                                        <!-- Aquí se cargarán los proveedores -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap core JavaScript -->
            <script src="assets/vendor/jquery/jquery.min.js"></script>
            <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
            <script src="assets/vendor/chart.js/Chart.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                window.getProvidersUrl = "{{ url('get-providers') }}";
            </script>
            <script src="{{ asset('js/order.js') }}"></script>
        </div>
    </div>
    </div>
</body>

</html>