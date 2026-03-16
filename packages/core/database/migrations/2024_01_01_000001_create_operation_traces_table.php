<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_traces', function (Blueprint $table): void {
            $table->id();
            $table->uuid('trace_id')->index();
            $table->string('operation');
            $table->string('type');
            $table->string('class');
            $table->float('duration_ms');
            $table->json('metadata');
            $table->string('status');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_traces');
    }
};
