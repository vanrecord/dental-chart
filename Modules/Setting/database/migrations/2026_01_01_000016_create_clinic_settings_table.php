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
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->string('khmer_title')->nullable();
            $table->string('khmer_title_color', 10)->default('#000000');
            $table->string('english_title')->nullable();
            $table->string('english_title_color', 10)->default('#000000');
            $table->text('description')->nullable();
            $table->string('description_color', 10)->default('#555555');
            $table->string('facebook_account')->nullable();
            $table->string('facebook_page')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->json('except_doctor_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};

