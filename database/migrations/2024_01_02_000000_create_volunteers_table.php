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
        Schema::create('volunteers', function (Blueprint $table) {
            $table->ulid('volunteer_id')->primary();
            $table->string('email')->collation('utf8mb4_unicode_ci');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->string('phone'); // Format: (XXX) XXX-XXXX
            $table->date('clean_date');
            $table->enum('probation_status', ['not_probation', 'active_probation', 'probation_complete']);
            $table->string('treatment_facility')->nullable();
            $table->string('facility_name')->nullable();
            $table->date('discharge_date')->nullable();
            $table->enum('gender', ['male', 'female', 'non_binary', 'prefer_not_to_say', 'other']);
            $table->string('neighborhood')->nullable();
            $table->string('bus_line')->nullable();
            $table->boolean('is_sms_deliverable')->default(true);
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('email');
            $table->index('clean_date');
            $table->index('probation_status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};
