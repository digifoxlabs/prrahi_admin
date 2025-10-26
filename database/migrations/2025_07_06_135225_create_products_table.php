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

Schema::create('products', function (Blueprint $table) {

            $table->id();

            // Parent ID for variants
            $table->foreignId('parent_id')->nullable()->constrained('products')->nullOnDelete();

            // Product basic details
            $table->string('name')->nullable();
            $table->string('code')->nullable();

            // Type: simple, variable, or variant
            $table->enum('type', ['simple', 'variable', 'variant'])->default('simple');

            // Segment and Sub-segment
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('unit')->nullable();

            // Pricing and stock-related fields
            $table->integer('dozen_per_case')->nullable();
            $table->decimal('mrp_per_unit', 10, 2)->nullable();
            $table->decimal('ptr_per_dozen', 10, 2)->nullable();
            $table->decimal('ptd_per_dozen', 10, 2)->nullable();
            $table->decimal('weight_gm', 10, 2)->nullable();
            $table->string('size')->nullable();

            $table->boolean('has_free_qty')->default(true);
            $table->integer('free_dozen_per_case')->nullable();

            // Variant attributes
            $table->json('attributes')->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
