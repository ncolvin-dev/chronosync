<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->ulid('volunteer_id')->primary();
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->string('phone');
            $table->date('clean_date');
            $table->string('probation_status')->default('not_probation');
            $table->string('treatment_facility')->nullable();
            $table->string('facility_name')->nullable();
            $table->date('discharge_date')->nullable();
            $table->string('gender');
            $table->string('neighborhood')->nullable();
            $table->string('bus_line')->nullable();
            $table->boolean('is_sms_deliverable')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};
