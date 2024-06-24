<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Etiquetas y Catalogo</title>
    <!-- Bootstrap core CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/sb-admin-2.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body id="page-top">
    <div id="wrapper">
        @include('slidebar')
        <div id="content-wrapper" class="d-flex flex-column dash" style="overflow-y: hidden;">
            <div id="content">
                @include('navbar')
                <div class="container-fluid mt-4">
                    <div class="row mb-3 gx-2 align-items-end">
                        <div class="col-md-2">
                            <label for="sku" class="form-label text-center w-100">SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control form-control-sm" value="{{ request('sku') }}" onkeyup="filtrar()">
                        </div>
                        <div class="col-md-2">
                            <label for="name" class="form-label text-center w-100">NOMBRE</label>
                            <input type="text" name="name" id="name" class="form-control form-control-sm" value="{{ request('name') }}" onkeyup="filtrar()">
                        </div>
                        <div class="col-md-2">
                            <label for="linea" class="form-label text-center w-100">LINEA</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" id="linea-addon">LN</span>
                                <input type="text" name="linea" id="linea" class="form-control" value="{{ request('linea') ? str_replace('LN', '', request('linea')) : '' }}" onkeyup="filtrar()" aria-describedby="linea-addon">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="sublinea" class="form-label text-center w-100">SUBLINEA</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" id="sublinea-addon">SB</span>
                                <input type="text" name="sublinea" id="sublinea" class="form-control" value="{{ request('sublinea') ? str_replace('SB', '', request('sublinea')) : '' }}" onkeyup="filtrar()" aria-describedby="sublinea-addon">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="departamento" class="form-label text-center w-100">DEPARTAMENTO</label>
                            <input type="text" name="departamento" id="departamento" class="form-control form-control-sm" value="{{ request('departamento') }}" onkeyup="filtrar()">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-secondary btn-sm w-100" onclick="limpiarFiltros()">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>
                    <!-- Tabla de datos -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive small-font">
                                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>PRODUCTO</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>SKU</th>
                                            <th>CODIGO BARRAS</th>
                                            <th>DEPARTAMENTO</th>
                                            <th>LINEA</th>
                                            <th>SUBLINEA</th>
                                            <th>EXHIBICIÓN</th>
                                            <th>CENTRO DE COSTOS</th>
                                            <th>TIPO STOCK</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody id="proveedorTable">
                                        @foreach($labels as $label)
                                        <tr>
                                            <td>{{ $label->INPRODID }}</td>
                                            <td>{{ $label->INPRODDSC }}</td>
                                            <td>{{ $label->INPRODI2 }}</td>
                                            <td>{{ $label->INPRODCBR }}</td>
                                            <td>{{ $label->INPR02ID }}</td>
                                            <td>{{ $label->INPR03ID }}</td>
                                            <td>{{ $label->INPR04ID }}</td>
                                            <td>{{ $label->Exhibicion }}</td>
                                            <td>{{ $label->CentroCostos }}</td>
                                            <td>{{ $label->TipoStock }}</td>


                                            <td>
                                                <button class="btn btn-secondary" onclick="showPrintModal('{{ $label->INPRODI2 }}', '{{ $label->INPRODDSC }}')">Imprimir SKU</button> </form>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div id="pagination-links">
                                    @if ($labels instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                    {{ $labels->appends(request()->query())->links() }}
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
    </div>

    <!-- Modal para seleccionar la cantidad de etiquetas a imprimir -->

    <div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printModalLabel">Imprimir Etiquetas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="printForm" method="POST" action="{{ route('print.label') }}" target="_blank">
                        @csrf
                        <input type="hidden" name="sku" id="modalSku">
                        <input type="hidden" name="description" id="modalDescription">
                        <div class="form-group">
                            <label for="quantity">Cantidad de etiquetas</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" form="printForm" class="btn btn-primary">Imprimir</button>
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
    <!-- Bootstrap core JavaScript -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="{{ asset('js/label.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>