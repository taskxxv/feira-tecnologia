
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Atribuir usernames aos usuários antigos.
        DB::table('usuarios')
            ->where('id', 1)
            ->whereNull('username')
            ->update(['username' => 'teste_enzo']);

        DB::table('usuarios')
            ->where('id', 2)
            ->whereNull('username')
            ->update(['username' => 'malu']);

        // Validar o formato: letras minúsculas,
        // números e underline, de 3 a 30 caracteres.
        DB::statement("
            ALTER TABLE usuarios
            ADD CONSTRAINT usuarios_username_formato_valido
            CHECK (username ~ '^[a-z0-9_]{3,30}$')
        ");

        // Tornar obrigatório.
        DB::statement("
            ALTER TABLE usuarios
            ALTER COLUMN username SET NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE usuarios
            ALTER COLUMN username DROP NOT NULL
        ");

        DB::statement("
            ALTER TABLE usuarios
            DROP CONSTRAINT IF EXISTS usuarios_username_formato_valido
        ");
    }
};
