<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    protected $signature = 'user:create {name} {email} {password} {role?}';
    protected $description = 'Crear un nuevo usuario con rol';

    public function handle()
    {
        $role = $this->argument('role') ?? 'user';
        
        // Validar que el rol sea válido
        if (!in_array($role, ['super_admin', 'admin', 'user'])) {
            $this->error('El rol debe ser: super_admin, admin o user');
            return 1;
        }

        try {
            $user = User::create([
                'name' => $this->argument('name'),
                'email' => $this->argument('email'),
                'password' => Hash::make($this->argument('password')),
                'role' => $role,
            ]);

            $this->info("Usuario creado exitosamente:");
            $this->info("Nombre: {$user->name}");
            $this->info("Email: {$user->email}");
            $this->info("Rol: {$user->role}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error al crear usuario: " . $e->getMessage());
            return 1;
        }
    }
}