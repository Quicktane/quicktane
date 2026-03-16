<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('media_file_id')->constrained('media_files')->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->string('label')->nullable();
            $table->boolean('is_main')->default(false);

            $table->unique(['product_id', 'media_file_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};
