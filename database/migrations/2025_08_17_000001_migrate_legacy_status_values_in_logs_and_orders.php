<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var list<string> */
    private array $legacyActiveStatuses = [
        'confirmed',
        'in_production',
        'quality_check',
        'ready',
        'delivered',
    ];

    public function up(): void
    {
        foreach ($this->legacyActiveStatuses as $legacyStatus) {
            DB::table('orders')->where('status', $legacyStatus)->update(['status' => 'accepted']);

            DB::table('production_logs')->where('from_status', $legacyStatus)->update(['from_status' => 'accepted']);
            DB::table('production_logs')->where('to_status', $legacyStatus)->update(['to_status' => 'accepted']);
        }

        DB::table('orders')->where('status', 'cancelled')->update(['status' => 'rejected']);

        DB::table('production_logs')->where('from_status', 'cancelled')->update(['from_status' => 'rejected']);
        DB::table('production_logs')->where('to_status', 'cancelled')->update(['to_status' => 'rejected']);
    }

    public function down(): void
    {
        // Legacy values cannot be restored reliably.
    }
};
