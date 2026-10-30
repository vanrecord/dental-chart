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
        if (!Schema::hasColumn('invoices', 'subtotal')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('subtotal', 15, 2)->default(0.00)->after('invoice_date');
                $table->decimal('discount', 15, 2)->default(0.00)->after('subtotal');
                $table->decimal('tax', 15, 2)->default(0.00)->after('discount');
            });
        }

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('type', 32); // 'service' or 'product'
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('reception_id')->nullable();
            $table->string('item_name');
            $table->date('operate_date');
            $table->string('tooth_numbers')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('tax', 15, 2)->default(0.00);
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            $table->foreign('reception_id')->references('id')->on('receptions')->nullOnDelete();
            $table->index(['invoice_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');

        if (Schema::hasColumn('invoices', 'subtotal')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn(['subtotal', 'discount', 'tax']);
            });
        }
    }
};

