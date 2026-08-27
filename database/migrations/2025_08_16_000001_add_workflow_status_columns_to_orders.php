<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('task_status')->default('pending')->after('status');
            $table->string('production_status')->nullable()->after('task_status');
        });

        DB::table('orders')->where('status', 'in_production')->update([
            'status' => 'accepted',
            'task_status' => 'accepted',
            'production_status' => 'in_production',
        ]);

        DB::table('orders')->where('status', 'quality_check')->update([
            'status' => 'accepted',
            'task_status' => 'accepted',
            'production_status' => 'quality_check',
        ]);

        DB::table('orders')->whereIn('status', ['ready', 'delivered'])->update([
            'status' => 'accepted',
            'task_status' => 'accepted',
            'production_status' => 'ready_to_pickup',
        ]);

        DB::table('orders')->where('status', 'confirmed')->update([
            'status' => 'accepted',
            'task_status' => 'pending',
        ]);

        DB::table('orders')->where('status', 'cancelled')->update([
            'status' => 'rejected',
            'task_status' => 'rejected',
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['task_status', 'production_status']);
        });
    }
};
