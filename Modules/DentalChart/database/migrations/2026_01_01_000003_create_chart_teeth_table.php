<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chart_teeth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dental_chart_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('tooth_number');
            $table->string('tooth_type'); // permanent or primary
            $table->string('tooth_status')->default('present');
            $table->text('general_notes')->nullable();
            $table->timestamps();
            $table->unique(['dental_chart_id','tooth_number']);
        });
    }
    public function down(): void { Schema::dropIfExists('chart_teeth'); }
};
