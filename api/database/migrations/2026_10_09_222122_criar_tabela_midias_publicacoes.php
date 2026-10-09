
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midias_publicacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')
                ->constrained('publicacoes')
                ->cascadeOnDelete();

            $table->string('tipo', 20);
            $table->text('url');
            $table->timestamp('created_at')->useCurrent();

            $table->index('post_id');
        });

        DB::statement("
            ALTER TABLE midias_publicacoes
            ADD CONSTRAINT midias_tipo_valido
            CHECK (tipo IN ('imagem', 'video'))
        ");

        DB::statement("
            ALTER TABLE midias_publicacoes
            ADD CONSTRAINT midias_url_nao_vazia
            CHECK (length(trim(url)) > 0)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('midias_publicacoes');
    }
};
