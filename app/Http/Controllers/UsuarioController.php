<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
   
    public function index()
    {
        $usuarios = User::all();
    
        // Ordenar manualmente los usuarios
        $usuariosOrdenados = $usuarios->sort(function ($a, $b) {
            $orden = ['superadmin'=>1,'admin'=>2,'usuario'=>3,'visita'=>4,'disabled'=>5];

    
            return $orden[$a->role] <=> $orden[$b->role];
        });
    
        return view('usuarios.index', compact('usuariosOrdenados'));
    }
    

    public function create()
{
    return view('usuarios.create');
}


public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|string', 
    ]);

    $user = new User;
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = bcrypt($request->password);
    $user->role = $request->role; // Asigna el valor del formulario al campo 'role'.
    $user->save();

    return redirect()->route('usuarios.index');
}



   // Método para mostrar el formulario de edición
   public function edit($id)
   {
       $usuario = User::findOrFail($id);
       return view('usuarios.create', compact('usuario')); 
   }
   

// Método para actualizar un recurso en la base de datos
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Actualización de datos básicos
    $user->name = $request->name;
    $user->email = $request->email;
    $user->role = $request->role; // Suponiendo que también estás actualizando el rol

    // Actualización de contraseña (solo si se proporciona una nueva)
    if ($request->filled('password')) {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user->password = bcrypt($request->password);
    }

    $user->save();

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado con éxito');
}


public function destroy($id)
{
    $Usuario = User::with('ventas')->findOrFail($id);

    if ($Usuario->ventas->count() > 0) {
        return redirect()->route('usuarios.index')->with('error', 'No se puede eliminar el Usuario porque tiene ventas asociadas');
    }

    $Usuario->delete();

    return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado con éxito');
}


}
