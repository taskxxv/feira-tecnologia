
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curtidas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->foreignId('post_id')
                ->constrained('publicacoes')
                ->cascadeOnDelete();

            $table->timestamp('created_at')->useCurrent();

            $table->unique(
                ['user_id', 'post_id'],
                'curtidas_usuario_publicacao_unique'
            );

            $table->index('post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curtidas');
    }
};
