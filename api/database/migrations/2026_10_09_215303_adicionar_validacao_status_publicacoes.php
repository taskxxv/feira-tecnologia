
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE publicacoes
            ADD CONSTRAINT publicacoes_status_valido
            CHECK (status IN ('pendente', 'aprovado', 'reprovado'))
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE publicacoes
            DROP CONSTRAINT IF EXISTS publicacoes_status_valido
        ");
    }
};
