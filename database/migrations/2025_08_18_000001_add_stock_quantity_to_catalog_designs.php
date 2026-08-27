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
            $table->unsignedInteger('stock_quantity')->default(0)->after('availability_status');
        });

        DB::table('catalog_designs')->where('availability_status', 'available')->update(['stock_quantity' => 1]);
    }

    public function down(): void
    {
        Schema::table('catalog_designs', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
        });
    }
};
