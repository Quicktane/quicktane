<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('source_id')->constrained('inventory_sources')->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->integer('reserved')->default(0);
            $table->integer('notify_quantity')->default(5);
            $table->boolean('is_in_stock')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
