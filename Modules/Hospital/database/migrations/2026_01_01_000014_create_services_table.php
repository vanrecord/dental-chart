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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id')->nullable()->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->string('code')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0.00);
            $table->decimal('cost', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->string('type', 32)->index(); // 'service' or 'product'
            $table->integer('qty')->default(0);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('commission', 15, 2)->default(0.00);
            $table->string('commission_type', 32)->default('fixed');
            $table->string('status', 32)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->index(['type', 'code']);
            $table->index(['hospital_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

