<?php

// app/Http/Controllers/LabelcatalogController.php
namespace App\Http\Controllers;
use App\Models\Insdos;//No sirve
use App\Models\LabelCatalog;//No sirve
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;


class LabelcatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            if ($user) {
                $userRoles = $user->roles;
                view()->share('userRoles', $userRoles);
            }

            return $next($request);
        });

    }
 
    public function labelscatalog(Request $request)
{
    $skuFilter = $request->input('sku');
    $nameFilter = $request->input('name');
    $lineaFilter = $request->input('linea');
    $sublineaFilter = $request->input('sublinea');
    $departamentoFilter = $request->input('departamento');
    $productIdFilter = $request->input('product_id');

    // Obtener el usuario actual
    $user = Auth::user();
    
    // Obtener los centros de costos asignados al usuario
    $centrosCostosIds = $user->costCenters->pluck('cost_center_id');

    // Si todos los filtros están vacíos o contienen solo valores por defecto, retornar una vista sin datos
    if (empty($skuFilter) && empty($nameFilter) && (empty($lineaFilter) || $lineaFilter == 'LN') && (empty($sublineaFilter) || $sublineaFilter == 'SB') && empty($departamentoFilter)) {
        return view('etiquetascatalogo', ['labels' => collect()]); // Retornar una colección vacía si no hay filtros
    }

    // Construir la consulta base
    $query = DB::table('INSDOS')
        ->join('INPROD', 'INSDOS.INPRODID', '=', 'INPROD.INPRODID')
        ->leftJoin('INALPR', function($join) {
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
            'INSDOS.INSDOSQDS as Exhibicion',
            'INSDOS.INALMNID as CentroCostos',
            'INALPR.INAPR17ID as TipoStock'
        )
        ->whereNotNull('INPROD.INPRODDSC')
        ->where('INPROD.INPRODDSC', '<>', '')
        ->whereNotIn('INPROD.INPRODDSC', ['.', '*', '..', '...', '....'])
        ->whereRaw('LEN(INPROD.INPRODI2) > 6')
        ->where(function($query) {
            $query->where('INALPR.INAPR17ID', '!=', '-1')
                  ->orWhere(function($query) {
                      $query->where('INALPR.INAPR17ID', '=', 'X')
                            ->where('INSDOS.INSDOSQDS', '>', 0);
                  });
        });

    // Añadir filtros basados en los inputs del usuario
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
    if (!empty($productIdFilter)) { 
        $query->where('INPROD.INPRODID', 'like', $productIdFilter . '%');
    }

    // Añadir filtro para los centros de costos asignados al usuario
    $query->whereIn('INSDOS.INALMNID', $centrosCostosIds);

    // Paginación de los resultados
    $labels = $query->paginate(20);

    return view('etiquetascatalogo', compact('labels'));
}

    
       

    public function printLabel(Request $request)
    {
        $sku = $request->input('sku');
        $description = $request->input('description');
        $quantity = $request->input('quantity', 1);
    
        $generator = new BarcodeGeneratorHTML();
        $barcodeHtml = $generator->getBarcode($sku, $generator::TYPE_CODE_128);
    
        $data = [
            'sku' => $sku,
            'description' => $description,
            'barcode' => $barcodeHtml
        ];
    
        $labels = array_fill(0, $quantity, $data);
    
        $pdf = Pdf::loadView('label', ['labels' => $labels]);
        $pdfOutput = $pdf->output();
    
        return response($pdfOutput, 200)->header('Content-Type', 'application/pdf');
    }
}
