<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tooth_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_tooth_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dental_condition_id')->nullable()->constrained('dental_conditions')->nullOnDelete();
            $table->string('surface', 12); // top, bottom, left, right, center, whole
            $table->string('custom_color', 7)->nullable();
            $table->text('note')->nullable();
            $table->string('treatment_status')->default('observed');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['chart_tooth_id','surface']);
        });
    }
    public function down(): void { Schema::dropIfExists('tooth_findings'); }
};
