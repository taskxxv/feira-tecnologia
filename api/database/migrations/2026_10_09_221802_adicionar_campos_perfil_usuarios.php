
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('username', 30)->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar_url', 2048)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('pais', 100)->nullable();
        });

        DB::statement("
            CREATE UNIQUE INDEX usuarios_username_unique_ci
            ON usuarios (LOWER(username))
        ");
    }

    public function down(): void
    {
        DB::statement("
            DROP INDEX IF EXISTS usuarios_username_unique_ci
        ");

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'bio',
                'avatar_url',
                'telefone',
                'pais',
            ]);
        });
    }
};
