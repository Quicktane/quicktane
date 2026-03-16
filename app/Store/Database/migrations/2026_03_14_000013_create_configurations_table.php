<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configurations', function (Blueprint $table): void {
            $table->id();
            $table->string('scope');
            $table->unsignedBigInteger('scope_id')->default(0);
            $table->string('path');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['scope', 'scope_id', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
