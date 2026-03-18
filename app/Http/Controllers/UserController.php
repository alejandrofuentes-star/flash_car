<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Listar todos los usuarios
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('users.index', compact('users'));
    }

    // Mostrar un usuario específico
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.detalles', compact('user'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('users.create');
    }

    // Crear nuevo usuario
    public function store(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => [
                'required',
                Rule::in(['super_admin', 'admin', 'user'])
            ],
        ]);

        // Verificar que Admin no pueda crear Super Admins
        if (auth()->user()->isAdmin() && $validated['role'] === 'super_admin') {
            return back()
                ->withInput()
                ->withErrors(['role' => 'No tienes permisos para crear Super Admins']);
        }

        // Crear el usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', "Usuario '{$user->name}' creado exitosamente");
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Verificar permisos
        if (auth()->user()->isAdmin() && $user->isSuperAdmin()) {
            abort(403, 'No tienes permisos para editar Super Admins');
        }
        
        return view('users.edit', compact('user'));
    }

    // Actualizar usuario
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Verificar permisos
        if (auth()->user()->isAdmin() && $user->isSuperAdmin()) {
            abort(403, 'No tienes permisos para editar Super Admins');
        }

        // Validación
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => [
                'required',
                Rule::in(['super_admin', 'admin', 'user'])
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verificar que Admin no pueda asignar rol super_admin
        if (auth()->user()->isAdmin() && $validated['role'] === 'super_admin') {
            return back()->withErrors(['role' => 'No tienes permisos para asignar el rol de Super Admin']);
        }

        // Actualizar datos
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        // Solo actualizar contraseña si se proporciona
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Verificar que no se pueda eliminar a sí mismo
        if ($user->id === auth()->user()->id) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propia cuenta');
        }

        // Verificar permisos según el rol
        if (auth()->user()->isSuperAdmin()) {
            // Super Admin puede eliminar a cualquiera (excepto a sí mismo)
            $userName = $user->name;
            $user->delete();
            return redirect()->route('users.index')->with('success', "Usuario '{$userName}' eliminado exitosamente");
            
        } elseif (auth()->user()->isAdmin()) {
            // Admin solo puede eliminar usuarios normales
            if ($user->isUser()) {
                $userName = $user->name;
                $user->delete();
                return redirect()->route('users.index')->with('success', "Usuario '{$userName}' eliminado exitosamente");
            } else {
                return redirect()->route('users.index')->with('error', 'Solo puedes eliminar usuarios normales');
            }
        }

        // Si llega aquí, no tiene permisos
        abort(403, 'No tienes permisos para eliminar usuarios');
    }
}