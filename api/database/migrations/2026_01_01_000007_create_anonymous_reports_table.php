<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anonymous_reports', function (Blueprint $table): void {
            $table->id();
            $table->text('original_content');
            $table->jsonb('ai_analysis')->nullable();
            $table->string('status', 20)->default('nova')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anonymous_reports');
    }
};
