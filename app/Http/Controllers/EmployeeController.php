<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
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
    // Método para mostrar la lista de empleados
    public function employees()
    {
        $employees = Employee::paginate(10); // Obtener 10 empleados por página
        return view('employees', compact('employees'));
    }

    // Método para mostrar los detalles de un empleado
    public function show($id)
    {
        $employee = Employee::findOrFail($id); // Buscar al empleado por su ID
        return view('employees.show', compact('employee'));
    }

    // Método para guardar un nuevo empleado
    public function store(Request $request)
    {
        // Valida los datos del formulario
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'curp' => 'required',
            'rfc' => 'required',
            'birth' => 'required',
        ]);

        // Crea un nuevo objeto Employee con los datos del formulario
        $employee = new Employee();
        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->middle_name = $request->input('middle_name');
        $employee->curp = $request->input('curp');
        $employee->rfc = $request->input('rfc');
        $employee->colony = $request->input('colony');
        $employee->street = $request->input('street');
        $employee->external_number = $request->input('external_number');
        $employee->internal_number = $request->input('internal_number');
        $employee->postal_code = $request->input('postal_code');
        $employee->phone = $request->input('phone');
        $employee->phone2 = $request->input('phone2');

        // Formatea la fecha al formato deseado (DD-MM-YYYY)
        $formattedBirth = date('d-m-Y', strtotime($request->input('birth')));
        $employee->birth = $formattedBirth;

        // Guarda el empleado en la base de datos
        $employee->save();

        // Redirecciona a una ruta adecuada después de guardar el empleado
        return redirect()->route('employees'); // Ajusta 'nombre_de_la_ruta' según tu aplicación
    }

    // Método para actualizar un empleado
    public function update(Request $request, $id)
{
    // Validar los campos del formulario
    $validatedData = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'curp' => 'required|string|max:18',
        'rfc' => 'required|string|max:13',
        'colony' => 'nullable|string|max:255',
        'street' => 'nullable|string|max:255',
        'external_number' => 'nullable|string|max:255',
        'internal_number' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:10',
        'phone' => 'nullable|string|max:15',
        'phone2' => 'nullable|string|max:15',
        'birth' => 'nullable|date',
        'status' => 'required|boolean'
    ]);

    // Encontrar el empleado por su ID
    $employee = Employee::findOrFail($id);

    // Formatear la fecha al formato d-m-Y
    if ($request->has('birth')) {
        $formattedBirth = date('d-m-Y', strtotime($request->birth));
    } else {
        $formattedBirth = $employee->birth; // Mantener la fecha actual si no se envió una nueva
    }

    // Actualizar los datos del empleado
    $employee->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'middle_name' => $request->middle_name,
        'curp' => $request->curp,
        'rfc' => $request->rfc,
        'colony' => $request->colony,
        'street' => $request->street,
        'external_number' => $request->external_number,
        'internal_number' => $request->internal_number,
        'postal_code' => $request->postal_code,
        'phone' => $request->phone,
        'phone2' => $request->phone2,
        'birth' => $formattedBirth,
        'status' => $request->status,
    ]);

    // Actualizar el estado del usuario asociado
    $user = User::where('employee_id', $employee->id)->first();
    if ($user) {
        $user->status = $employee->status;
        $user->save();
    }

    // Redirigir con un mensaje de éxito
    return redirect()->route('employees')->with('success', 'Empleado y usuario actualizados correctamente.');
}

     
}
