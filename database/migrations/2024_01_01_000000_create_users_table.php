<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('user_id')->primary();
            $table->string('email')->unique()->collation('utf8mb4_unicode_ci');
            $table->string('password_hash');
            $table->json('roles')->default(json_encode([])); // ['admin', 'coordinator', 'viewer']
            $table->timestamp('last_login')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
