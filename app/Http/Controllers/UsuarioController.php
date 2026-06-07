<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Congregacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    // Solo superadmin y secretario pueden administrar usuarios
    private function puedeAdministrarUsuarios()
    {
        abort_if(!in_array(auth()->user()->role, ['superadmin', 'secretario']), 403);
    }

    // Buscar usuario respetando la congregación
    private function buscarUsuarioSeguro($id)
    {
        $query = User::query();

        if (auth()->user()->role !== 'superadmin') {
            $query->where('congregacion_id', auth()->user()->congregacion_id);
        }

        return $query->findOrFail($id);
    }

    public function index()
    {
        $this->puedeAdministrarUsuarios();

        $query = User::query();

        if (auth()->user()->role !== 'superadmin') {
            $query->where('congregacion_id', auth()->user()->congregacion_id);
        }

        $usuarios = $query->get();

        // Ordenar manualmente los usuarios
        $usuariosOrdenados = $usuarios->sort(function ($a, $b) {
            $orden = [
                'superadmin' => 1,
                'secretario' => 2,
                'colaborador' => 3,
                'tablero' => 4,
                'disabled' => 5,
            ];

            return ($orden[$a->role] ?? 99) <=> ($orden[$b->role] ?? 99);
        });

        return view('usuarios.index', compact('usuariosOrdenados'));
    }

    public function create()
    {
        $this->puedeAdministrarUsuarios();

        $congregaciones = auth()->user()->role === 'superadmin'
            ? Congregacion::orderBy('nombre')->get()
            : collect();

        return view('usuarios.create', compact('congregaciones'));
    }

    public function store(Request $request)
    {
        $this->puedeAdministrarUsuarios();

        $rolesPermitidos = auth()->user()->role === 'superadmin'
            ? ['superadmin', 'secretario', 'colaborador', 'tablero', 'disabled']
            : ['secretario', 'colaborador', 'tablero', 'disabled'];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in($rolesPermitidos)],
            'congregacion_id' => auth()->user()->role === 'superadmin'
                ? ['required', 'exists:congregacions,id']
                : ['nullable'],
        ]);

        $user = new User;
        $user->congregacion_id = auth()->user()->role === 'superadmin'
            ? $request->congregacion_id
            : auth()->user()->congregacion_id;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado con éxito');
    }

    // Método para mostrar el formulario de edición
    public function edit($id)
    {
        $this->puedeAdministrarUsuarios();

        $usuario = $this->buscarUsuarioSeguro($id);

        $congregaciones = auth()->user()->role === 'superadmin'
            ? Congregacion::orderBy('nombre')->get()
            : collect();

        return view('usuarios.create', compact('usuario', 'congregaciones'));
    }

    // Método para actualizar un recurso en la base de datos
    public function update(Request $request, $id)
    {
        $this->puedeAdministrarUsuarios();

        $user = $this->buscarUsuarioSeguro($id);

        $rolesPermitidos = auth()->user()->role === 'superadmin'
            ? ['superadmin', 'secretario', 'colaborador', 'tablero', 'disabled']
            : ['secretario', 'colaborador', 'tablero', 'disabled'];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in($rolesPermitidos)],
            'congregacion_id' => auth()->user()->role === 'superadmin'
                ? ['required', 'exists:congregacions,id']
                : ['nullable'],
        ]);

        // Actualización de datos básicos
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if (auth()->user()->role === 'superadmin') {
            $user->congregacion_id = $request->congregacion_id;
        }

        // Actualización de contraseña solo si se proporciona una nueva
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado con éxito');
    }

    public function destroy($id)
    {
        $this->puedeAdministrarUsuarios();

        $usuario = $this->buscarUsuarioSeguro($id);

        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No podés eliminar tu propio usuario.');
        }


        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado con éxito');
    }
}