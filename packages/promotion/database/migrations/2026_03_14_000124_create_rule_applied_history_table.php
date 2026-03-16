<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rule_applied_history', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cart_price_rule_id')->constrained('cart_price_rules')->cascadeOnDelete();
            $table->unsignedBigInteger('cart_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('discount_amount', 12, 4);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_applied_history');
    }
};
