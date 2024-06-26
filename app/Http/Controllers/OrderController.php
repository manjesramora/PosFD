<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Providers;
use App\Models\StoreCostCenter;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Obtener el usuario autenticado
            $user = Auth::user();

            if ($user) {
                // Obtener los roles del usuario autenticado
                $userRoles = $user->roles;

                // Compartir los roles del usuario con todas las vistas
                view()->share('userRoles', $userRoles);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Obtenemos la fecha actual y restamos 6 meses
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

        // Aplicamos el filtro para no mostrar registros más antiguos de 6 meses
        $query->where('ACMVOIFDOC', '>=', $sixMonthsAgo);

        // Validar los centros de costo asociados al usuario
        $user = Auth::user();
        if ($user) {
            $centrosCostosIds = $user->costCenters->pluck('cost_center_id');
            $query->whereIn('ACMVOIALID', $centrosCostosIds); // Filtrar por ACMVOIALM en lugar de center_id
        }

        // Ordenamos por ACMVOIDOC en orden descendente
        $query->orderBy('ACMVOIDOC', 'desc');

        // Incluir el nombre del almacén ACMVOIALM en la consulta
        $query->with('store');

        $orders = $query->paginate(10);

        return view('orders', compact('orders'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('query');
        $field = $request->get('field');

        $providers = Providers::where($field, 'LIKE', "%{$query}%")
            ->whereBetween('CNCDIRID', [30000000, 49999999])
            ->take(10)
            ->get();

        return response()->json($providers);
    }
}
