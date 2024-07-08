<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Providers;
use App\Models\StoreCostCenter;
use App\Models\Receptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class OrderController extends Controller
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
    public function index(Request $request)
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        
        if (!$user) {
            // Manejo de error si el usuario no está autenticado
            return redirect()->route('login');
        }
    
        // Obtener los IDs de los centros de costos asociados al usuario
        $centrosCostosIds = $user->costCenters->pluck('cost_center_id')->toArray();
    
        $query = Order::query();
    
        // Filtrar por centros de costos asociados al usuario
        if (!empty($centrosCostosIds)) {
            $query->whereIn('ACMVOIALID', $centrosCostosIds);
        }
    
        // Filtrar registros dentro de los últimos 6 meses
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $query->where('ACMVOIFDOC', '>=', $sixMonthsAgo);
    
        // Aplicar filtros adicionales
        if ($request->filled('ACMVOIDOC')) {
            $query->where('ACMVOIDOC', $request->input('ACMVOIDOC'));
        }
    
        if ($request->filled('CNCDIRID')) {
            $query->where('CNCDIRID', $request->input('CNCDIRID'));
        }
    
        if ($request->filled('CNCDIRNOM')) {
            $query->whereHas('provider', function ($q) use ($request) {
                $q->where('CNCDIRNOM', 'like', '%' . $request->input('CNCDIRNOM') . '%');
            });
        }
    
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('ACMVOIFDOC', [$request->input('start_date'), $request->input('end_date')]);
        }
    
        // Aplicar ordenamiento solo para columnas válidas
        $sortableColumns = ['CNTDOCID', 'ACMVOIDOC', 'CNCDIRID', 'ACMVOIFDOC', 'ACMVOIALID'];
        $sortColumn = $request->input('sortColumn', 'ACMVOIDOC');
        $sortDirection = $request->input('sortDirection', 'desc');
    
        if (in_array($sortColumn, $sortableColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('ACMVOIDOC', 'desc'); // Valor por defecto en caso de columnas inválidas
        }
    
        // Obtener las órdenes paginadas
        $orders = $query->paginate(10);
    
        if ($request->ajax()) {
            return view('orders_table', compact('orders', 'sortColumn', 'sortDirection'))->render();
        }
    
        return view('orders', compact('orders', 'sortColumn', 'sortDirection'));
    }
    public function autocomplete(Request $request)
{
    $query = $request->input('query');
    $field = $request->input('field');

    $results = DB::table('CNCDIR')
        ->where($field, 'LIKE', "%{$query}%")
        ->get(['CNCDIRID', 'CNCDIRNOM']);

    return response()->json($results);
}
public function showReceptions($ACMVOIDOC)
{
    $order = Order::where('ACMVOIDOC', $ACMVOIDOC)
        ->with('provider')
        ->first();

    $receptions = Receptions::where('ACMVOIDOC', $ACMVOIDOC)->get();
    $provider = Providers::where('CNCDIRID', $order->CNCDIRID)->first();

    $cntdoc = DB::table('cntdoc')
        ->where('cntdocid', 'RCN')
        ->first();

    if ($cntdoc && isset($cntdoc->CNTDOCNSIG)) {
        $num_rcn_letras = $cntdoc->CNTDOCNSIG;

        if (is_numeric($num_rcn_letras)) {
            $new_value = intval($num_rcn_letras) + 1;
        } else {
            $new_value = chr(ord($num_rcn_letras) + 1);
        }

        DB::table('cntdoc')
            ->where('cntdocid', 'RCN')
            ->update(['CNTDOCNSIG' => $new_value]);
    } else {
        $num_rcn_letras = 'NUMERO';
    }

    $currentDate = now()->toDateString();

    // Obtén todas las partidas de acmvor1 para el documento especificado
    $partidas = DB::table('acmvor1')
        ->where('ACMVOIDOC', $ACMVOIDOC)
        ->get();

    // Filtra las partidas para excluir las que cumplen con las condiciones
    $filteredPartidas = $partidas->filter(function ($partida) {
        // Verifica si hay coincidencias en acmroi
        $acmroi = DB::table('acmroi')
            ->where('ACMROIDOC', $partida->ACMVOIDOC)
            ->where('ACMROILIN', $partida->ACMVOILIN)
            ->first();

        // Excluye la partida si se encuentra un acmroi con ACACTLID != 'CANCELADO' y ACMROIQTTR == ACMVOIQTO
        if ($acmroi) {
            // Si ACACTLID no es 'CANCELADO' y las cantidades son iguales
            if ($acmroi->ACACTLID != 'CANCELADO' && $acmroi->ACMROIQTTR == $partida->ACMVOIQTO) {
                return false;  // Excluir la partida
            }
        }

        // Mantiene la partida si no se cumplen las condiciones de exclusión
        return true;
    });

    return view('receptions', compact('receptions', 'order', 'provider', 'num_rcn_letras', 'currentDate', 'filteredPartidas'));
}






}
