<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rule_conditions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cart_price_rule_id')->constrained('cart_price_rules')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('rule_conditions')->cascadeOnDelete();
            $table->string('type');
            $table->string('attribute')->nullable();
            $table->string('operator')->nullable();
            $table->text('value')->nullable();
            $table->string('aggregator')->nullable();
            $table->boolean('is_inverted')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_conditions');
    }
};
