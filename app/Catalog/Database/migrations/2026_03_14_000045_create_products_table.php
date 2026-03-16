<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('type')->default('simple');
            $table->foreignId('attribute_set_id')->constrained('attribute_sets')->restrictOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('base_price', 12, 4);
            $table->decimal('special_price', 12, 4)->nullable();
            $table->dateTime('special_price_from')->nullable();
            $table->dateTime('special_price_to')->nullable();
            $table->decimal('cost', 12, 4)->nullable();
            $table->decimal('weight', 8, 4)->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('sku');
            $table->index('slug');
            $table->index('is_active');
            $table->index('attribute_set_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
