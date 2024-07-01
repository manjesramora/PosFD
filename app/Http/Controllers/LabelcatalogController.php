<?php

// app/Http/Controllers/LabelcatalogController.php
namespace App\Http\Controllers;

use App\Models\Insdos; // Importa el modelo Insdos (aunque no se usa en este controlador)
use App\Models\LabelCatalog; // Importa el modelo LabelCatalog (aunque no se usa en este controlador)
use Illuminate\Http\Request; // Importa la clase Request para manejar las solicitudes HTTP
use Illuminate\Support\Facades\Auth; // Importa la clase Auth para la autenticación de usuarios
use Picqer\Barcode\BarcodeGeneratorHTML; // Importa la clase BarcodeGeneratorHTML para generar códigos de barras en formato HTML
use Barryvdh\DomPDF\Facade\Pdf; // Importa la clase Pdf para generar archivos PDF
use Illuminate\Support\Facades\DB; // Importa la clase DB para realizar consultas a la base de datos

class LabelcatalogController extends Controller
{
    // Constructor del controlador
    public function __construct()
    {
        // Middleware para compartir los roles del usuario en las vistas
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            if ($user) {
                $userRoles = $user->roles;
                view()->share('userRoles', $userRoles);
            }

            return $next($request);
        });
    }

    // Método para manejar la lógica de la vista del catálogo de etiquetas
    public function labelscatalog(Request $request)
    {
        // Filtrar inputs de la solicitud
        $productIdFilter = $request->input('productId');
        $skuFilter = $request->input('sku');
        $nameFilter = $request->input('name');
        $lineaFilter = $request->input('linea');
        $sublineaFilter = $request->input('sublinea');
        $departamentoFilter = $request->input('departamento');
        $sortColumn = $request->input('sort', 'INPROD.INPRODID'); // Columna por defecto
        $sortDirection = $request->input('direction', 'asc'); // Dirección por defecto

        // Obtener el usuario actual
        $user = Auth::user();

        // Obtener los centros de costos asignados al usuario
        $centrosCostosIds = $user->costCenters->pluck('cost_center_id');

        // Construir la consulta base
        $query = DB::table('INSDOS')
            ->join('INPROD', 'INSDOS.INPRODID', '=', 'INPROD.INPRODID')
            ->leftJoin('INALPR', function ($join) {
                $join->on('INSDOS.INPRODID', '=', 'INALPR.INPRODID')
                    ->on('INSDOS.INALMNID', '=', 'INALPR.INALMNID');
            })
            ->select(
                'INPROD.INPRODID',
                'INPROD.INPRODDSC',
                'INPROD.INPRODDS2',
                'INPROD.INPRODDS3',
                'INPROD.INPRODI2',
                'INPROD.INPRODI3',
                'INPROD.INTPCMID',
                'INPROD.INPR02ID',
                'INPROD.INPR03ID',
                'INPROD.INPR04ID',
                'INPROD.INPRODCBR',
                'INPROD.INTPALID',
                DB::raw('ROUND(INSDOS.INSDOSQDS, 2) as Existencia'), // Formatear a 2 decimales
                'INSDOS.INALMNID as CentroCostos',
                'INALPR.INAPR17ID as TipoStock'
            )
            // Condiciones para INPRODDSC (nombre del producto)
            ->whereNotNull('INPROD.INPRODDSC')
            ->where('INPROD.INPRODDSC', '<>', '')
            ->where('INPROD.INPRODDSC', '<>', '.')
            ->where('INPROD.INPRODDSC', '<>', '*')
            ->where('INPROD.INPRODDSC', '<>', '..')
            ->where('INPROD.INPRODDSC', '<>', '...')
            ->where('INPROD.INPRODDSC', '<>', '....')
            // Condición para Tipo de Stock no vacío
            ->whereNotNull('INALPR.INAPR17ID')
            ->where('INALPR.INAPR17ID', '<>', '')
            ->where('INALPR.INAPR17ID', '<>', '-1')
            // Condiciones para Tipo de Almacenamiento
            ->whereNotIn('INPROD.INTPALID', ['O', 'D'])
            ->whereRaw('ISNUMERIC(INPROD.INTPALID) = 0') // Excluir valores numéricos en Tipo de Almacenamiento
            // Condición para la longitud de SKU (código de producto)
            ->whereRaw('LEN(INPROD.INPRODI2) >= 7')
            // Aplicar ordenamiento
            ->orderBy($sortColumn, $sortDirection);

        // Añadir filtros basados en los inputs del usuario
        if (!empty($productIdFilter)) {
            $query->where('INPROD.INPRODID', 'like', $productIdFilter . '%');
        }
        if (!empty($skuFilter)) {
            $query->where('INPROD.INPRODI2', 'like', $skuFilter . '%');
        }
        if (!empty($nameFilter)) {
            $query->where('INPROD.INPRODDSC', 'like', $nameFilter . '%');
        }
        if (!empty($lineaFilter) && $lineaFilter !== 'LN') {
            $query->where('INPROD.INPR03ID', 'like', $lineaFilter . '%');
        }
        if (!empty($sublineaFilter) && $sublineaFilter !== 'SB') {
            $query->where('INPROD.INPR04ID', 'like', $sublineaFilter . '%');
        }
        if (!empty($departamentoFilter)) {
            $query->where('INPROD.INPR02ID', 'like', $departamentoFilter . '%');
        }

        // Añadir filtro para los centros de costos asignados al usuario
        $query->whereIn('INSDOS.INALMNID', $centrosCostosIds);

        // Paginación de los resultados
        $labels = $query->paginate(20)->appends($request->query());

        // Retornar la vista con los datos filtrados y paginados
        return view('etiquetascatalogo', compact('labels'));
    }

    // Método para imprimir la etiqueta
    public function printLabel(Request $request)
{
    // Obtener inputs de la solicitud
    $sku = $request->input('sku');
    $description = $request->input('description');
    $quantity = $request->input('quantity', 1);

    // Generar el código de barras en formato HTML
    $generator = new BarcodeGeneratorHTML();
    $barcodeHtml = $generator->getBarcode($sku, $generator::TYPE_CODE_128);

    // Preparar los datos para la etiqueta
    $data = [
        'sku' => $sku,
        'description' => $description,
        'barcode' => $barcodeHtml
    ];

    // Crear un array de etiquetas con la cantidad especificada
    $labels = array_fill(0, $quantity, $data);

    // Generar el PDF con las etiquetas
    $pdf = Pdf::loadView('label', ['labels' => $labels]);

    // Guardar el PDF temporalmente
    $pdfOutput = $pdf->output();
    $filename = 'labels.pdf';
    file_put_contents(public_path($filename), $pdfOutput);

    // Retornar la ruta del PDF temporal
    return response()->json(['url' => asset($filename)]);
}

}
