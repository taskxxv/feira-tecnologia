
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE denuncias_anonimas
            ADD CONSTRAINT denuncias_conteudo_nao_vazio
            CHECK (length(trim(original_content)) > 0)
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE denuncias_anonimas
            DROP CONSTRAINT IF EXISTS denuncias_conteudo_nao_vazio
        ");
    }
};
