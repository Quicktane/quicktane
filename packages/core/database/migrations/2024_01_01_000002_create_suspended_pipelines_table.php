<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suspended_pipelines', function (Blueprint $table): void {
            $table->id();
            $table->uuid('token')->unique();
            $table->string('pipeline_name');
            $table->string('status')->index();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('guest_token')->nullable();
            $table->json('context');
            $table->json('completed_steps');
            $table->string('current_step');
            $table->text('reason');
            $table->json('metadata');
            $table->json('result')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suspended_pipelines');
    }
};
