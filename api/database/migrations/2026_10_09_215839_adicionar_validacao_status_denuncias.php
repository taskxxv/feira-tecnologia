
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE denuncias_anonimas
            ADD CONSTRAINT denuncias_status_valido
            CHECK (
                status IN (
                    'nova',
                    'em_analise',
                    'resolvida',
                    'arquivada'
                )
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE denuncias_anonimas
            DROP CONSTRAINT IF EXISTS denuncias_status_valido
        ");
    }
};
