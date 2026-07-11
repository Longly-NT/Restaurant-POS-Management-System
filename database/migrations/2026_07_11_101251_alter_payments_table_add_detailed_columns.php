<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove old misaligned columns safely
            if (Schema::hasColumn('payments', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('payments', 'method')) {
                $table->dropColumn('method');
            }
            if (Schema::hasColumn('payments', 'payer_label')) {
                $table->dropColumn('payer_label');
            }

            // Add the new columns matching your Payment Model exactly
            $table->decimal('subtotal_amount', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('tip_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->string('discount_reason')->nullable();
            $table->unsignedBigInteger('discount_authorized_by')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('payment_method'); // e.g. cash, card, mobile
            $table->unsignedBigInteger('processed_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Rollback columns if needed
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('method')->nullable();
            $table->string('payer_label')->nullable();

            $table->dropColumn([
                'subtotal_amount', 'tax_amount', 'tip_amount', 
                'discount_amount', 'discount_reason', 'discount_authorized_by', 
                'refund_amount', 'total_amount', 'payment_method', 'processed_by'
            ]);
        });
    }
};