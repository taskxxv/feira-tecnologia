<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'users' => 'usuarios',
            'subjects' => 'disciplinas',
            'posts' => 'publicacoes',
            'comments' => 'comentarios',
            'post_embeddings' => 'vetores_publicacoes',
            'anonymous_reports' => 'denuncias_anonimas',
        ];

        foreach ($tables as $old => $new) {
            if (Schema::hasTable($old) && ! Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }

        if (Schema::hasTable('usuarios')) {
            Schema::table('usuarios', function (Blueprint $table): void {
                if (Schema::hasColumn('usuarios', 'email_verified_at')) {
                    $table->renameColumn('email_verified_at', 'email_verificado_em');
                }
                if (Schema::hasColumn('usuarios', 'remember_token')) {
                    $table->renameColumn('remember_token', 'token_lembrete');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('usuarios')) {
            Schema::table('usuarios', function (Blueprint $table): void {
                if (Schema::hasColumn('usuarios', 'email_verificado_em')) {
                    $table->renameColumn('email_verificado_em', 'email_verified_at');
                }
                if (Schema::hasColumn('usuarios', 'token_lembrete')) {
                    $table->renameColumn('token_lembrete', 'remember_token');
                }
            });
        }

        $tables = [
            'denuncias_anonimas' => 'anonymous_reports',
            'vetores_publicacoes' => 'post_embeddings',
            'comentarios' => 'comments',
            'publicacoes' => 'posts',
            'disciplinas' => 'subjects',
            'usuarios' => 'users',
        ];

        foreach ($tables as $old => $new) {
            if (Schema::hasTable($old) && ! Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }
    }
};
