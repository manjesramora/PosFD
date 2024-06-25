<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    
    // Método para mostrar la lista de roles
    public function roles()
    {
        $roles = Role::all(); // Obtener todos los roles
        $permissions = Permission::all(); // Obtener todos los permisos
        return view('roles', ['roles' => $roles, 'permissions' => $permissions]); // Pasar los roles y permisos a la vista roles.blade.php
    }

    // Método para mostrar los detalles de un rol
    public function show($id)
    {
        $role = Role::findOrFail($id); // Buscar al rol por su ID
        return view('roles.show', compact('role'));
    }

    public function createRoleForm()
    {
        $permissions = Permission::all(); // Obtener todos los permisos
        return view('role', ['permissions' => $permissions]);
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'name' => 'required|unique:roles|max:100',
            'description' => 'required|unique:roles|max:200',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Crear un nuevo rol
        $role = new Role();
        $role->name = strtoupper($request->name);
        $role->description = $request->description;

        // Guardar el rol en la base de datos
        $role->save();

        // Guardar permisos seleccionados
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        // Redirigir a la página de la lista de roles u otra página apropiada
        return redirect()->route('roles')->with('success', 'Rol creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        // Validar los campos del formulario
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
    
        // Encontrar el rol por su ID
        $role = Role::findOrFail($id);
    
        // Actualizar los datos del rol
        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
    
        // Actualizar los permisos seleccionados
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->detach(); // Eliminar todos los permisos si no se seleccionaron ninguno
        }
    
        // Redirigir con un mensaje de éxito
        return redirect()->route('roles')->with('success', 'Rol actualizado correctamente.');
    }

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
    
                // Obtener todos los permisos y compartirlos con todas las vistas
                $permissions = Permission::all();
                view()->share('permissions', $permissions);
    
                return $next($request);
            });
        }
    

    public function destroy($id)
    {
        // Encontrar el rol por su ID
        $role = Role::findOrFail($id);

        // Desvincular todos los permisos asociados
        $role->permissions()->detach();

        // Eliminar el rol
        $role->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('roles')->with('success', 'Rol eliminado correctamente.');
    }
}
