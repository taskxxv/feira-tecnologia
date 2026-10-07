<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Credencial apenas para desenvolvimento local. Troque a senha
        // imediatamente antes de disponibilizar a aplicação.
        User::firstOrCreate(
            ['email' => 'admin@escola.local'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'role' => User::ROLE_ADMIN,
            ]
        );
    }
}
