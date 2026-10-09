
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensagens_ia', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->string('papel', 20);
            $table->text('conteudo');

            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'id']);
        });

        DB::statement("
            ALTER TABLE mensagens_ia
            ADD CONSTRAINT mensagens_ia_papel_valido
            CHECK (papel IN ('usuario', 'assistente'))
        ");

        DB::statement("
            ALTER TABLE mensagens_ia
            ADD CONSTRAINT mensagens_ia_conteudo_nao_vazio
            CHECK (length(trim(conteudo)) > 0)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens_ia');
    }
};
