<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('payment_method_code');
            $table->string('type');
            $table->string('status')->default('pending');
            $table->decimal('amount', 12, 4);
            $table->string('currency_code', 3);
            $table->string('reference_id')->nullable();
            $table->foreignId('parent_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('reference_id');
            $table->index('payment_method_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
