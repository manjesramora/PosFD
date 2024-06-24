<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Permission;
use App\Models\StoreCostCenter;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showDashFDForm()
    {
        $users = User::all();
        $roles = Role::all();
        $permissions = Permission::all();
        $user = Auth::user();
        $employee = Employee::find($user->employee_id);
        $userRoles = $user->roles;
        $userCenters = $user->centers;
    
        return view('index', [
            'users' => $users, 
            'roles' => $roles, 
            'permissions' => $permissions,
            'employee' => $employee,
            'userRoles' => $userRoles,
            'userCenters' => $userCenters,
        ]);
    }

    public function showUserIndexForm()
    {
        return view('indexes');
    }

    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        $permissions = Permission::all();
        $user = Auth::user();
        $employee = Employee::find($user->employee_id);
        $userRoles = $user->roles;
        $userCenters = $user->centers;
    
        return view('users', [
            'users' => $users, 
            'roles' => $roles, 
            'permissions' => $permissions,
            'employee' => $employee,
            'userRoles' => $userRoles,
            'userCenters' => $userCenters
        ]);
    }
    
    public function createUserForm()
    {
        $employees = Employee::all();
        $roles = Role::all();
        $centers = StoreCostCenter::all(); // Obtener todos los centros de costo

        return view('user', ['employees' => $employees, 'roles' => $roles, 'centers' => $centers]);
    }

    public function checkUsername(Request $request)
    {
        $baseUsername = $request->get('base');
        $username = ($baseUsername);
        $counter = 1;
    
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
    
        return response()->json(['username' => $username]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|max:100',
            'password' => 'required|min:6',
            'employee_id' => 'required|exists:employees,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'center' => 'nullable|array', 
            'center.*' => 'exists:store_cost_centers,id', 
        ]);

        $user = new User();
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->employee_id = $request->employee_id;

        $user->save();

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        if ($request->has('center')) { 
            $user->costCenters()->sync($request->center); 
        }

        return redirect()->route('users')->with('success', 'Usuario creado correctamente.');
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'centers' => 'nullable|array', // Agregando validación para centros
            'centers.*' => 'exists:store_cost_centers,id', // Validando cada centro de costo
            'status' => 'required|boolean',
            'password' => 'nullable|min:6',
        ]);
    
        $user = User::findOrFail($id);
    
        $user->update([
            'username' => $request->username,
            'employee_id' => $request->employee_id,
            'status' => $request->status,
        ]);
    
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->save();
    
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }
    
        if ($request->has('centers')) {
            $user->costCenters()->sync($request->centers);
        } else {
            $user->costCenters()->detach();
        }
    
        return redirect()->route('users')->with('success', 'Usuario actualizado correctamente.');
    }
    
    public function resetPassword(User $user)
    {
        // Actualizar contraseña y campos relacionados
        $user->password = bcrypt('Ferre01@');
        $user->failed_login_attempts = 0;
        $user->locked = 0;
        $user->save();

        return response()->json(['message' => 'Contraseña restablecida con éxito.']);
    }
    protected $employees;
    protected $centers;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            view()->share('user', $user);
            $this->employees = Employee::all();
            view()->share('employees', $this->employees);
            $this->centers = StoreCostCenter::all();
            view()->share('centers', $this->centers);

            return $next($request);
        });
    }
}
