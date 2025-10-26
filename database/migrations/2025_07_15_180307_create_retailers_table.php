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

            // Linked to Distributor
            $table->foreignId('distributor_id')->constrained()->onDelete('cascade');

            $table->date('appointment_date');
            $table->string('retailer_name');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('town');
            $table->string('district');
            $table->string('state');
            $table->string('pincode');
            $table->string('landmark')->nullable();
            $table->string('contact_person');
            $table->string('contact_number');
            $table->string('email');
            $table->string('gst')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_anniversary')->nullable();
            $table->string('nature_of_outlet')->nullable();

            // Appointed By Information
            $table->string('appointed_by')->nullable();
            $table->string('designation')->nullable();

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
