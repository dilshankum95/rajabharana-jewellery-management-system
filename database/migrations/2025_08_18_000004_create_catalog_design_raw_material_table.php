<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_design_raw_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_design_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_required', 12, 3);
            $table->timestamps();

            $table->unique(['catalog_design_id', 'raw_material_id'], 'catalog_material_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_design_raw_material');
    }
};
