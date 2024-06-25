<?php
// Archivo: app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Providers;
use App\Models\CntDoc;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Dompdf\Options;
use App\Models\Product;

class OrderController extends Controller
{

    public function getProviders()
    {
        $proveedores = Providers::whereBetween('CNCDIRID', [30000000, 40000000])->get();
        return response()->json($proveedores);
    }
    public function searchView(Request $request)
{
    // Verificar si el usuario está autenticado
    if (!Auth::check()) {
        // Redirigir al usuario a la página de inicio de sesión si no está autenticado
        return redirect()->route('login');
    }

    $user = Auth::user();

    // Obtener los centros de costo permitidos para el usuario
    $allowedCostCenters = $user->costCenters->pluck('cost_center_id');

    // Si no hay centros de costo permitidos, retornar una vista vacía
    if ($allowedCostCenters->isEmpty()) {
        return view('search', ['orders' => null]); // Pasar null en lugar de una colección vacía
    }

    // Calcular la fecha límite de 6 meses atrás
    $sixMonthsAgo = now()->subMonths(6)->format('Y-m-d');

    // Construir la consulta principal
    $query = Order::query()
        ->where('ACMROIBGP', '=', 'N')
        ->whereIn('INALMNID', $allowedCostCenters)
        ->whereDate('ACMROIFDOC', '>=', $sixMonthsAgo); // Agregar condición para filtrar por fecha

    // Aplicar filtros opcionales
    if ($request->filled('ACMROIDOC')) {
        $query->where('ACMROIDOC', $request->input('ACMROIDOC'));
    }

    if ($request->filled('CNCDIRID')) {
        $query->where('CNCDIRID', $request->input('CNCDIRID'));
    }

    if ($request->filled('start_date')) {
        $query->whereDate('ACMROIFDOC', '>=', $request->input('start_date'));
    }

    if ($request->filled('end_date')) {
        $query->whereDate('ACMROIFDOC', '<=', $request->input('end_date'));
    }

    $orders = $query->select([
        'CNTDOCID',
        'ACMROIDOC',
        'ACMROIFDOC',
        'INALMNID',
        'CNCDIRID',
        'ACMROICXP',
        DB::raw('MAX(CNCIASID) as CNCIASID')
    ])
    ->groupBy('CNTDOCID', 'ACMROIDOC', 'ACMROIFDOC', 'INALMNID', 'CNCDIRID', 'ACMROICXP')
    ->orderBy('ACMROIFDOC', 'desc')
    ->Paginate(7);

    $providerIds = $orders->pluck('CNCDIRID')->unique();

    // Verificar si hay proveedores asociados
    $providers = [];
    if ($orders->isNotEmpty() && $providerIds->isNotEmpty()) {
        $providers = Providers::whereIn('CNCDIRID', $providerIds)->pluck('CNCDIRNOM', 'CNCDIRID');
    }

    return view('search', compact('orders', 'providers'));
}

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
    public function recepcionar(Request $request)
    {
        $data = $request->input('data');

        // Verificar si $data es null antes de intentar usarlo
        if ($data !== null) {
            $hasPendingItems = $request->input('hasPendingItems');

            DB::transaction(function () use ($data, $hasPendingItems) {
                foreach ($data as $item) {
                    $order = Order::where('ACMROILIN', $item['acmroilin'])
                        ->where('INPRODI2', $item['inprodi2'])
                        ->first();

                    if ($order) {
                        $cantidadFaltante = $item['acmroiqttr'] - $item['acmroiqt'];

                        // Actualizar la orden existente
                        $order->ACMROIQT = $item['acmroiqt'];
                        $order->ACMROINP = $item['acmroinp'];
                        $order->ACMROING = $item['acmroing'];
                        $order->ACMROICXP = 'N';
                        $order->save();

                        // Si falta cantidad, crear nuevo documento
                        if ($cantidadFaltante > 0 && $hasPendingItems) {
                            // Generar nuevo ACMROINDOC único
                            $nuevoDoc = $this->generateUniqueDocNumber();

                            // Crear nueva orden con cantidad faltante
                            Order::create([
                                'ACMROITDOC' => $order->ACMROITDOC,
                                'ACMROINDOC' => $nuevoDoc,
                                'ACMROILIN' => $order->ACMROILIN,
                                'ACMROIDSC' => $order->ACMROIDSC,
                                'ACMROIUMT' => $order->ACMROIUMT,
                                'INPRODI2' => $order->INPRODI2,
                                'INPRODCBR' => $order->INPRODCBR,
                                'ACMROIPESOU' => $order->ACMROIPESOU,
                                'ACMROIVOLU' => $order->ACMROIVOLU,
                                'ACMROIQTTR' => $cantidadFaltante,
                                'ACMROIQT' => 0,
                                'ACMROINP' => $order->ACMROINP,
                                'ACMROING' => $order->ACMROING,
                                'ACMROICXP' => 'N',
                            ]);
                        }
                    }
                }
            });
        }

        return response()->json(['status' => 'success']);
    }
    public function recepcion(Request $request)
{
    $ACMROIDOC = $request->input('ACMROIDOC');
    $orders = Order::where('ACMROIDOC', $ACMROIDOC)->get();
    $nextNumber = $this->getNextRcnNumber(); // Llama al método para obtener el próximo número
    return view('reception', compact('orders', 'nextNumber'));
}
private function getNextRcnNumber()
{
    // Busca el registro correspondiente en la base de datos
    $cntDoc = CntDoc::where('CNTDOCID', 'RCN')->first();

    // Verifica si se encontró el registro
    if ($cntDoc) {
        // Obtén el número actual
        $currentNumber = $cntDoc->CNTDOCNSIG;
        // Incrementa el número para el próximo documento
        $cntDoc->CNTDOCNSIG += 1;
        // Guarda los cambios en la base de datos
        $cntDoc->save();

        // Retorna el número actual para su uso
        return $currentNumber;
    }

    // Si no se encuentra el registro, retorna null
    return null;
}

    public function generateReport(Request $request)
    {
        // Obtener el valor del ACMROIDOC desde la solicitud
        $ACMROIDOC = $request->input('ACMROIDOC');

        // Buscar las órdenes asociadas al ACMROIDOC
        $orders = Order::where('ACMROIDOC', $ACMROIDOC)->get();

        // Verificar si se encontraron órdenes
        if ($orders->isEmpty()) {
            // Si no se encontraron órdenes, redirigir con un mensaje de error
            return redirect()->back()->with('error', 'No se encontraron órdenes asociadas al número de documento proporcionado.');
        }

        // Generar el contenido del PDF
        $pdf_content = view('reportrcn', compact('orders'))->render();

        // Configurar las opciones de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        // Definir el tamaño de la página como horizontal (landscape)
        $options->set('defaultPaperSize', 'landscape');

        // Crear una instancia de Dompdf con las opciones configuradas
        $dompdf = new Dompdf($options);

        // Cargar el contenido HTML en Dompdf
        $dompdf->loadHtml($pdf_content);

        // Renderizar el PDF
        $dompdf->render();

        // Obtener el contenido del PDF como una cadena
        $pdf_content = $dompdf->output();

        // Descargar el PDF
        return response($pdf_content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment;filename="reporte.pdf"');
    }
    // OrderController.php

    public function getProvidersByNumber(Request $request)
    {
        $number = $request->input('number');
        $proveedores = Providers::where('CNCDIRID', 'like', '%' . $number . '%')
            ->whereBetween('CNCDIRID', [40000000, 49999999])
            ->get();
        return response()->json($proveedores);
    }

    public function getProvidersByName(Request $request)
    {
        $name = $request->input('name');
        $proveedores = Providers::where('CNCDIRNOM', 'like', '%' . $name . '%')
            ->whereBetween('CNCDIRID', [40000000, 49999999])
            ->get();
        return response()->json($proveedores);
    }
}
