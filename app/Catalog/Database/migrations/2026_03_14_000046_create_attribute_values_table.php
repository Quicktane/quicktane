<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->text('value')->nullable();

            $table->unique(['product_id', 'attribute_id']);
        });

        DB::statement('ALTER TABLE `attribute_values` ADD INDEX `attribute_values_attribute_id_value_index` (`attribute_id`, `value`(255))');
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
