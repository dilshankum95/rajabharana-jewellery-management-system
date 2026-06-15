<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereNull('phone')->orWhere('phone', '')->update(['phone' => '0770000000']);
        DB::table('users')->whereNull('address')->orWhere('address', '')->update(['address' => 'Not provided']);
        DB::table('users')->whereNull('city')->orWhere('city', '')->update(['city' => 'Colombo']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 25)->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->string('city', 100)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 25)->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('city', 100)->nullable()->change();
        });
    }
};
