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
        Schema::create('visit_notes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_person_id');

            // Polymorphic entity
            $table->unsignedBigInteger('entity_id');
            $table->enum('entity_type', ['distributor', 'retailer']);

            $table->text('message')->nullable();

            // 📍 GEO LOCATION
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();

            $table->index(['entity_id', 'entity_type']);
            $table->index('sales_person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_notes');
    }
};
