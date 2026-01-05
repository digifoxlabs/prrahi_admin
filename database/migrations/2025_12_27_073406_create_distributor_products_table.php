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
        Schema::create('distributor_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('distributor_id')
                ->constrained()
                ->cascadeOnDelete();

            // Reference to master product (optional but useful)
            $table->unsignedBigInteger('product_id')->nullable();

            // Snapshot fields (do NOT depend on master product later)
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->string('variant')->nullable();

            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('ptr', 10, 2)->nullable();

            $table->timestamps();

            $table->unique(['distributor_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_products');
    }
};
