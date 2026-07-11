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
            $table->foreignId('dining_table_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'open', 'sent_to_kitchen', 'accepted', 'preparing', 'finished', 'served', 'paid', 'cancelled',
            ])->default('open');
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamp('sent_to_kitchen_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
