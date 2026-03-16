<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('name');
            $table->uuid('uuid')->unique()->after('id');
            $table->string('first_name')->after('uuid');
            $table->string('last_name')->after('first_name');
            $table->foreignId('role_id')->nullable()->after('last_name')->constrained('roles')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('role_id');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropSoftDeletes();
            $table->dropColumn('last_login_at');
            $table->dropColumn('is_active');
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn('last_name');
            $table->dropColumn('first_name');
            $table->dropColumn('uuid');
            $table->string('name')->after('id');
        });
    }
};
