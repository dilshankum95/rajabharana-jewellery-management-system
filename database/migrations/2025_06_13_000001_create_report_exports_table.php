<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_exports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type', 100);
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('format', 20)->default('csv');
            $table->json('parameters')->nullable();
            $table->timestamps();

            $table->index('report_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_exports');
    }
};
