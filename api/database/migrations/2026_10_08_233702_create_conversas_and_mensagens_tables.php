<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_1_id')
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->foreignId('usuario_2_id')
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['usuario_1_id', 'usuario_2_id'],
                'conversas_dupla_unica'
            );

            $table->index('usuario_2_id');
        });

        // Mantém o menor ID primeiro e impede conversar consigo mesmo.
        DB::statement('
            ALTER TABLE conversas
            ADD CONSTRAINT conversas_ordem_usuarios
            CHECK (usuario_1_id < usuario_2_id)
        ');

        Schema::create('mensagens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversa_id')
                ->constrained('conversas')
                ->cascadeOnDelete();

            $table->foreignId('remetente_id')
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->text('conteudo')->nullable();

            $table->foreignId('post_id')
                ->nullable()
                ->constrained('publicacoes')
                ->nullOnDelete();

            $table->string('tipo', 20)->default('texto');
            $table->timestamp('lida_em')->nullable();
            $table->timestamps();

            $table->index(['conversa_id', 'id']);
            $table->index('remetente_id');
            $table->index('post_id');
        });

        DB::statement("
            ALTER TABLE mensagens
            ADD CONSTRAINT mensagens_tipo_valido
            CHECK (tipo IN ('texto', 'publicacao'))
        ");

        DB::statement("
            ALTER TABLE mensagens
            ADD CONSTRAINT mensagens_texto_valido
            CHECK (
                tipo <> 'texto'
                OR (
                    conteudo IS NOT NULL
                    AND length(trim(conteudo)) > 0
                    AND post_id IS NULL
                )
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens');
        Schema::dropIfExists('conversas');
    }
};