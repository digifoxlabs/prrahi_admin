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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the activity
            $table->nullableMorphs('actor');
            // actor_type, actor_id
            // SalesPerson / Distributor / Admin

            // What happened
            $table->string('activity_type');
            // order_created, order_updated, visit_logged, etc.

            // On which entity
            $table->nullableMorphs('subject');
            // Order / Retailer / Distributor / Visit

            // Location
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Extra data (order number, amount, notes, etc.)
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
