<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('family_visas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visa_id')->nullable(); // Foreign key to individual visas
            $table->string('full_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('tracking_id')->nullable(); // Removed unique
            $table->string('gmail')->nullable(); // Removed unique
            $table->string('pak_visa_password')->nullable();
            $table->string('gmail_password')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->date('date')->nullable();
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('visa_id')->references('id')->on('visas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_visas');
    }
};
