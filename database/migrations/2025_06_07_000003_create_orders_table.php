<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('design_type');
            $table->foreignId('catalog_design_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_image_path')->nullable();
            $table->string('item_type');
            $table->string('item_name')->nullable();
            $table->string('size')->nullable();
            $table->decimal('weight_grams', 8, 2)->nullable();
            $table->text('specifications')->nullable();
            $table->string('gold_quality');
            $table->string('gemstone_type')->nullable();
            $table->text('gemstone_details')->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->text('special_instructions')->nullable();
            $table->date('expected_delivery_date');
            $table->string('contact_phone', 20);
            $table->text('delivery_address')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('estimated_price', 12, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
