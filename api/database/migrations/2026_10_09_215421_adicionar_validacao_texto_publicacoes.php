
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE publicacoes
            ADD CONSTRAINT publicacoes_titulo_nao_vazio
            CHECK (length(trim(title)) > 0)
        ");

        DB::statement("
            ALTER TABLE publicacoes
            ADD CONSTRAINT publicacoes_conteudo_nao_vazio
            CHECK (length(trim(content)) > 0)
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE publicacoes
            DROP CONSTRAINT IF EXISTS publicacoes_conteudo_nao_vazio
        ");

        DB::statement("
            ALTER TABLE publicacoes
            DROP CONSTRAINT IF EXISTS publicacoes_titulo_nao_vazio
        ");
    }
};
