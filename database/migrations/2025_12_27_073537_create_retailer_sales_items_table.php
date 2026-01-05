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
        Schema::create('retailer_sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('retailer_sale_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('distributor_product_id')
                ->constrained('distributor_products')
                ->cascadeOnDelete();

            $table->integer('quantity');
            $table->decimal('rate', 10, 2)->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailer_sales_items');
    }
};
