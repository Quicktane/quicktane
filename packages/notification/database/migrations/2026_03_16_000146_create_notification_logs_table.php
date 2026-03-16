<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('channel')->default('email');
            $table->string('template_code');
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->json('variables')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('store_view_id')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('channel');
            $table->index('template_code');
            $table->index('recipient');
            $table->index('status');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
