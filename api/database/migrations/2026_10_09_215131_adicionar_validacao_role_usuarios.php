php artisan migrate<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE usuarios
            ADD CONSTRAINT usuarios_role_valido
            CHECK (role IN ('aluno', 'admin'))
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE usuarios
            DROP CONSTRAINT IF EXISTS usuarios_role_valido
        ");
    }
};
