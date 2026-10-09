
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE mensagens
            ADD CONSTRAINT mensagens_publicacao_valida
            CHECK (
                tipo <> 'publicacao'
                OR (post_id IS NOT NULL AND conteudo IS NULL)
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE mensagens
            DROP CONSTRAINT IF EXISTS mensagens_publicacao_valida
        ");
    }
};
