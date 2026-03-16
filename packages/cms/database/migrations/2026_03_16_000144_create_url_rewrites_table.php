<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('url_rewrites', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('request_path');
            $table->string('target_path');
            $table->unsignedSmallInteger('redirect_type')->nullable();
            $table->unsignedBigInteger('store_view_id')->default(0);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['request_path', 'store_view_id']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('store_view_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('url_rewrites');
    }
};
