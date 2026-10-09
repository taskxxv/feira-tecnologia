
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE comentarios
            ADD CONSTRAINT comentarios_conteudo_nao_vazio
            CHECK (length(trim(content)) > 0)
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE comentarios
            DROP CONSTRAINT IF EXISTS comentarios_conteudo_nao_vazio
        ");
    }
};
