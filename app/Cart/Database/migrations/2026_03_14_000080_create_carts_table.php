<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('store_id')->constrained('stores');
            $table->string('guest_token')->nullable()->unique();
            $table->string('status')->default('active');
            $table->string('currency_code', 3);
            $table->unsignedInteger('items_count')->default(0);
            $table->decimal('subtotal', 12, 4)->default(0);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['guest_token', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
