<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_block_store_views', function (Blueprint $table): void {
            $table->unsignedBigInteger('block_id');
            $table->unsignedBigInteger('store_view_id')->default(0);

            $table->primary(['block_id', 'store_view_id']);

            $table->foreign('block_id')
                ->references('id')
                ->on('cms_blocks')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_block_store_views');
    }
};
