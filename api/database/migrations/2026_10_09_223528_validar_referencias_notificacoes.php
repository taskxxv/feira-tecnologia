
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE notificacoes
            ADD CONSTRAINT notificacoes_referencias_validas
            CHECK (
                (
                    tipo IN ('curtida', 'comentario')
                    AND post_id IS NOT NULL
                    AND autor_id IS NOT NULL
                    AND mensagem_id IS NULL
                )
                OR
                (
                    tipo = 'mensagem'
                    AND mensagem_id IS NOT NULL
                    AND autor_id IS NOT NULL
                    AND post_id IS NULL
                )
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE notificacoes
            DROP CONSTRAINT IF EXISTS notificacoes_referencias_validas
        ");
    }
};
