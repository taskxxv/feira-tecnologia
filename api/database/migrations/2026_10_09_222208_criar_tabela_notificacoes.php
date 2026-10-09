
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->foreignId('autor_id')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();

            $table->string('tipo', 30);

            $table->foreignId('post_id')
                ->nullable()
                ->constrained('publicacoes')
                ->nullOnDelete();

            $table->foreignId('mensagem_id')
                ->nullable()
                ->constrained('mensagens')
                ->nullOnDelete();

            $table->timestamp('lida_em')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'lida_em', 'created_at']);
        });

        DB::statement("
            ALTER TABLE notificacoes
            ADD CONSTRAINT notificacoes_tipo_valido
            CHECK (tipo IN ('curtida', 'comentario', 'mensagem'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
