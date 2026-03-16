<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('increment_id')->unique();
            $table->foreignId('store_id')->constrained('stores');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_email');
            $table->unsignedBigInteger('customer_group_id')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('subtotal', 12, 4);
            $table->decimal('shipping_amount', 12, 4)->default(0);
            $table->decimal('discount_amount', 12, 4)->default(0);
            $table->decimal('tax_amount', 12, 4)->default(0);
            $table->decimal('grand_total', 12, 4);
            $table->decimal('total_paid', 12, 4)->default(0);
            $table->decimal('total_refunded', 12, 4)->default(0);
            $table->string('currency_code', 3);
            $table->string('shipping_method_code')->nullable();
            $table->string('shipping_method_label')->nullable();
            $table->string('payment_method_code')->nullable();
            $table->string('payment_method_label')->nullable();
            $table->string('coupon_code')->nullable();
            $table->integer('total_quantity');
            $table->decimal('weight', 12, 4)->nullable();
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
