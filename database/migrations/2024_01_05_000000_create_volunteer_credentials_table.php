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
        Schema::create('volunteer_credentials', function (Blueprint $table) {
            $table->ulid('credential_id')->primary();
            $table->foreignUlid('volunteer_id')->constrained('volunteers', 'volunteer_id')->onDelete('cascade');
            $table->foreignUlid('facility_id')->constrained('facilities', 'facility_id')->onDelete('cascade');
            $table->string('credential_type'); // Type name or ID
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
            $table->timestamp('approval_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('volunteer_id');
            $table->index('facility_id');
            $table->index('status');
            $table->index('expiration_date');
            $table->unique(['volunteer_id', 'facility_id', 'credential_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_credentials');
    }
};
