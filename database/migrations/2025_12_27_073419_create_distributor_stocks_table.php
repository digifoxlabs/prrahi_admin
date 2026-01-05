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
        Schema::create('distributor_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('distributor_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('distributor_product_id')
                  ->constrained('distributor_products')
                  ->cascadeOnDelete();

            $table->integer('available_qty')->default(0);

            $table->timestamps();

            $table->unique(['distributor_id', 'distributor_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_stocks');
    }
};
