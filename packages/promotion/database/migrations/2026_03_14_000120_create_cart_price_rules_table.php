<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_price_rules', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('stop_further_processing')->default(false);
            $table->string('action_type');
            $table->decimal('action_amount', 12, 4)->nullable();
            $table->decimal('max_discount_amount', 12, 4)->nullable();
            $table->boolean('apply_to_shipping')->default(false);
            $table->unsignedInteger('times_used')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_price_rules');
    }
};
