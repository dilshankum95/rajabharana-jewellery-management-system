<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->morphs('stockable');
            $table->decimal('quantity_before', 12, 3);
            $table->decimal('quantity_delta', 12, 3);
            $table->decimal('quantity_after', 12, 3);
            $table->string('reason');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['stockable_type', 'stockable_id', 'reason']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
