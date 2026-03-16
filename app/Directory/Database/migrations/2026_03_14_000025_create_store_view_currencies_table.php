<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_view_currencies', function (Blueprint $table): void {
            $table->foreignId('store_view_id')->constrained('store_views')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();

            $table->primary(['store_view_id', 'currency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_view_currencies');
    }
};
