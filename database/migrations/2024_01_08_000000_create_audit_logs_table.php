<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->ulid('audit_log_id')->primary();
            $table->ulid('actor_user_id');
            $table->foreign('actor_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->string('action');
            $table->string('entity_type');
            $table->string('entity_id');
            $table->json('change_details')->nullable();
            $table->text('reason')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index('actor_user_id');
            $table->index('entity_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
