<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_designs', function (Blueprint $table) {
            $table->string('gold_quality')->default('22k')->after('category');
            $table->decimal('weight_grams', 8, 2)->nullable()->after('gold_quality');
            $table->decimal('selling_price', 12, 2)->nullable()->after('description');
            $table->string('availability_status')->default('available')->after('selling_price');
        });

        foreach (DB::table('catalog_designs')->get() as $design) {
            DB::table('catalog_designs')->where('id', $design->id)->update([
                'gold_quality' => $design->default_gold_quality ?? '22k',
                'weight_grams' => $design->starting_weight_grams,
                'availability_status' => $design->is_active ? 'available' : 'out_of_stock',
                'selling_price' => 0,
            ]);
        }

        Schema::create('catalog_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_design_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        foreach (DB::table('catalog_designs')->whereNotNull('image_path')->get() as $design) {
            DB::table('catalog_images')->insert([
                'catalog_design_id' => $design->id,
                'image_path' => $design->image_path,
                'sort_order' => 0,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('catalog_designs', function (Blueprint $table) {
            $table->dropColumn(['default_gold_quality', 'starting_weight_grams', 'is_active', 'image_path']);
        });
    }

    public function down(): void
    {
        Schema::table('catalog_designs', function (Blueprint $table) {
            $table->string('image_path')->nullable();
            $table->string('default_gold_quality')->default('22k');
            $table->decimal('starting_weight_grams', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
        });

        Schema::dropIfExists('catalog_images');

        Schema::table('catalog_designs', function (Blueprint $table) {
            $table->dropColumn(['gold_quality', 'weight_grams', 'selling_price', 'availability_status']);
        });
    }
};
