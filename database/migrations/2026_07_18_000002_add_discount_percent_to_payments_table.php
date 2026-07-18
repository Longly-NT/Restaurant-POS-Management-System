<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Discounts are now entered as a percentage in the UI. We still keep
            // discount_amount (the resolved dollar value) for reporting, but we
            // also record the percentage that was actually applied so receipts
            // and audits show what was keyed in.
            $table->decimal('discount_percent', 5, 2)->nullable()->default(0)->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });
    }
};
