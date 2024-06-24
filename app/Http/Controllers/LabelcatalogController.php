<?php

// app/Http/Controllers/LabelcatalogController.php
namespace App\Http\Controllers;
use App\Models\Insdos;
use App\Models\LabelCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Barryvdh\DomPDF\Facade\Pdf;

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
        
        // Si todos los filtros están vacíos o contienen solo valores por defecto, retornar una vista sin datos
        if (empty($skuFilter) && empty($nameFilter) && (empty($lineaFilter) || $lineaFilter == 'LN') && (empty($sublineaFilter) || $sublineaFilter == 'SB') && empty($departamentoFilter)) {
            return view('etiquetascatalogo', ['labels' => collect()]); // Retornar una colección vacía si no hay filtros
        }

        // Construir la consulta base
        $query = Insdos::join('INPROD', 'INSDOS.INPRODID', '=', 'INPROD.INPRODID')
            ->join('INALPR', 'INPROD.INPRODID', '=', 'INALPR.INPRODID')
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
            ->whereIn('INALPR.INAPR17ID', ['M', 'P', 'B', 'X']); // Filtro por Tipo de Stock

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

        // Paginación de los resultados
        $labels = $query->paginate(20);

        return view('etiquetascatalogo', compact('labels'));
    }

       

    public function printLabel(Request $request)
    {
        $sku = $request->input('sku');
        $description = $request->input('description');

        $generator = new BarcodeGeneratorHTML();
        $barcodeHtml = $generator->getBarcode($sku, $generator::TYPE_CODE_128);

        $data = [
            'sku' => $sku,
            'description' => $description,
            'barcode' => $barcodeHtml
        ];

        $pdf = Pdf::loadView('label', $data);
        return $pdf->download('label.pdf');
    }
}
