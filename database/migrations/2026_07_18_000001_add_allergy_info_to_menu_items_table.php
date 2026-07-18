<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Short, plain-text allergy / dietary warning shown prominently on the
            // frontend (order screen + kitchen tickets) so guests with allergies
            // and staff can see it at a glance, separate from the marketing
            // description above.
            $table->text('allergy_info')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('allergy_info');
        });
    }
};
