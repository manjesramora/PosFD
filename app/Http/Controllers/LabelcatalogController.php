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
                'INPROD.INTPALID',
                DB::raw('ROUND(INSDOS.INSDOSQDS, 2) as Existencia'), // Formatear a 2 decimales
                'INSDOS.INALMNID as CentroCostos',
                'INALPR.INAPR17ID as TipoStock'
            )
            // Condiciones para INPRODDSC
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
            // Condición para la longitud de SKU
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
