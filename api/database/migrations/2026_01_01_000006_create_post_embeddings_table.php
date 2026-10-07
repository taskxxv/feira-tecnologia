<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_embeddings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('post_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE post_embeddings ADD COLUMN embedding vector(1536) NOT NULL');
        } else {
            Schema::table('post_embeddings', fn (Blueprint $table) => $table->text('embedding'));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('post_embeddings');
    }
};
