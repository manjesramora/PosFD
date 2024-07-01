<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Providers;
use App\Models\StoreCostCenter;
use App\Models\Receptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

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
        $sixMonthsAgo = now()->subMonths(6)->format('Y-m-d');

        $query = Order::query();

        if ($request->has('ACMROIDOC') && $request->ACMROIDOC != '') {
            $query->where('ACMROIDOC', 'LIKE', "%{$request->ACMROIDOC}%");
        }

        if ($request->has('CNCDIRID') && $request->CNCDIRID != '') {
            $query->where('CNCDIRID', 'LIKE', "%{$request->CNCDIRID}%");
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('ACMVOIFDOC', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('ACMVOIFDOC', '<=', $request->end_date);
        }

        $query->where('ACMVOIFDOC', '>=', $sixMonthsAgo);

        $user = Auth::user();
        if ($user) {
            $centrosCostosIds = $user->costCenters->pluck('cost_center_id');
            $query->whereIn('ACMVOIALID', $centrosCostosIds);
        }

        $query->orderBy('ACMVOIDOC', 'desc');
        $query->with('store');

        $orders = $query->paginate(10);

        return view('orders', compact('orders'));
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

    // Obtener el número de documento de la tabla cntdoc
    $cntdoc = DB::table('cntdoc')
        ->where('cntdocid', 'RCN')
        ->first();

    if ($cntdoc && isset($cntdoc->CNTDOCNSIG)) {
        $num_rcn_letras = $cntdoc->CNTDOCNSIG;

        // Incrementar el valor en 1 (si es un número)
        if (is_numeric($num_rcn_letras)) {
            $new_value = intval($num_rcn_letras) + 1;
        } else {
            // Si es una letra, cambia al siguiente carácter en el alfabeto
            $new_value = chr(ord($num_rcn_letras) + 1);
        }

        DB::table('cntdoc')
            ->where('cntdocid', 'RCN')
            ->update(['CNTDOCNSIG' => $new_value]);
    } else {
        $num_rcn_letras = 'NUMERO'; // Valor por defecto si no se encuentra el registro
    }

    // Obtener la fecha actual
    $currentDate = now()->toDateString();

    return view('receptions', compact('receptions', 'order', 'provider', 'num_rcn_letras', 'currentDate'));
}



}
