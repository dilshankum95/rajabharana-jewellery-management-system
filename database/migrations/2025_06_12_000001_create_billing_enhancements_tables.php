<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('tax_rate_percent', 5, 2)->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('category_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('category_code', 50)->unique();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('tax_rate_percent', 5, 2)->default(0)->after('tax');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('discount');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->restrictOnDelete();
            $table->string('payment_method', 50);
            $table->decimal('payment_amount', 12, 2);
            $table->string('payment_status')->default('completed');
            $table->date('payment_date');
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('payment_date');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('payments');
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['tax_rate_percent', 'discount_percent']);
        });
        Schema::dropIfExists('category_discounts');
        Schema::dropIfExists('billing_settings');
    }
};
