<?php

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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('hospital_id')->nullable()->index();
            $table->string('doctor_code', 50)->unique();
            $table->string('specialization', 255)->nullable()->index();
            $table->string('qualification', 255)->nullable();
            $table->string('license_number', 100)->nullable();
            $table->integer('experience_years')->default(0);
            $table->decimal('consultation_fee', 10, 2)->default(0.00);
            $table->text('bio')->nullable();
            $table->boolean('status')->default(true)->index(); // true = active, false = inactive
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

