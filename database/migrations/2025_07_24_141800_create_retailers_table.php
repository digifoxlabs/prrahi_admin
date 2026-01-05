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
        Schema::create('retailers', function (Blueprint $table) {
            $table->id();

            $table->string('retailer_name')->nullable();

            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('town')->nullable();

            $table->string('district')->nullable();
            $table->string('state')->nullable();

            $table->string('pincode', 10)->nullable();
            $table->string('landmark')->nullable();

            $table->string('contact_person');
            $table->string('contact_number', 20);
            $table->string('email')->nullable();

            $table->string('gst')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_anniversary')->nullable();
            $table->date('appointment_date')->nullable();
            $table->string('nature_of_outlet')->nullable();

            // ✅ Polymorphic appointed by
            $table->nullableMorphs('appointed_by'); 
            // creates appointed_by_id + appointed_by_type

            $table->foreignId('distributor_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailers');
    }
};
