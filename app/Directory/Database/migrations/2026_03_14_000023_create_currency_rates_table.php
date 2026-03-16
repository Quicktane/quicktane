<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table): void {
            $table->id();
            $table->char('base_currency_code', 3);
            $table->char('target_currency_code', 3);
            $table->decimal('rate', 24, 12);
            $table->timestamps();

            $table->unique(['base_currency_code', 'target_currency_code']);

            $table->foreign('base_currency_code')
                ->references('code')
                ->on('currencies')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('target_currency_code')
                ->references('code')
                ->on('currencies')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
