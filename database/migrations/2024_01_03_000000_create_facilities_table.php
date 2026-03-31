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
        Schema::create('facilities', function (Blueprint $table) {
            $table->ulid('facility_id')->primary();
            $table->string('facility_name')->unique();
            $table->string('address');
            $table->string('city');
            $table->char('state', 2); // US state abbreviation
            $table->char('zip', 5); // 5-digit ZIP code
            $table->string('main_phone'); // Format: (XXX) XXX-XXXX
            $table->string('contact_email');
            $table->integer('clean_time_requirement'); // Years
            $table->json('credentialing_types')->default(json_encode([])); // Array of credential type IDs
            $table->boolean('gender_restriction')->default(false);
            $table->boolean('probation_allowed')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('contact1_name')->nullable();
            $table->string('contact1_phone')->nullable();
            $table->string('contact1_email')->nullable();
            $table->string('contact2_name')->nullable();
            $table->string('contact2_phone')->nullable();
            $table->string('contact2_email')->nullable();
            $table->string('timezone')->default('America/Chicago');
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('facility_name');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
