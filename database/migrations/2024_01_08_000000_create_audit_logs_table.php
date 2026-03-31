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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->ulid('audit_id')->primary();
            $table->foreignUlid('actor_user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('action_type'); // e.g., 'assign_volunteer', 'override_buffer', 'update_credential'
            $table->string('table_name');
            $table->string('record_id');
            $table->json('change_details')->nullable(); // Details of what changed
            $table->timestamp('timestamp')->useCurrent();

            // Indexes
            $table->index('actor_user_id');
            $table->index('action_type');
            $table->index('table_name');
            $table->index('timestamp');
            $table->index(['table_name', 'record_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
