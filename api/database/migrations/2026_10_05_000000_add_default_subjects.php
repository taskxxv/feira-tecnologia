<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['Ideias', 'História', 'Matemática', 'Ciência', 'Leitura'] as $name) {
            DB::table('subjects')->insertOrIgnore([
                'name' => $name, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Preserve temas que podem ter publicações associadas.
    }
};
