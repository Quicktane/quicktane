<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zone_countries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('region_id')->nullable()->constrained('regions');

            $table->unique(['shipping_zone_id', 'country_id', 'region_id'], 'szc_zone_country_region_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zone_countries');
    }
};
