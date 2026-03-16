<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_zone_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tax_zone_id')->constrained('tax_zones')->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->string('postcode_from')->nullable();
            $table->string('postcode_to')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_zone_rules');
    }
};
