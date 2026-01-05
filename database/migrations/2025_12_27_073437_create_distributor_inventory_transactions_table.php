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
        Schema::create('distributor_inventory_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('distributor_id');
            $table->unsignedBigInteger('distributor_product_id');

            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');

            $table->nullableMorphs('source');
            $table->string('remarks')->nullable();

            $table->timestamps();

            // ✅ SHORT, SAFE FOREIGN KEY NAMES
            $table->foreign('distributor_id', 'dit_dist_fk')
                  ->references('id')
                  ->on('distributors')
                  ->cascadeOnDelete();

            $table->foreign('distributor_product_id', 'dit_dist_prod_fk')
                  ->references('id')
                  ->on('distributor_products')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_inventory_transactions');
    }
};
