<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credential_types', function (Blueprint $table) {
            $table->ulid('credential_type_id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->integer('expiration_days')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credential_types');
    }
};
