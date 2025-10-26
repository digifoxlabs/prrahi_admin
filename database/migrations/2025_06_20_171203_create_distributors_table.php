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



Schema::create('distributors', function (Blueprint $table) {
    $table->id();

    $table->foreignId('sales_persons_id')->nullable()->constrained('sales_persons')->onDelete('set null');
    
    $table->date('appointment_date');
    $table->string('firm_name');
    $table->string('nature_of_firm'); // Proprietorship, Partnership, LLP, Pvt Ltd, Ltd
    $table->string('address_line_1')->nullable();
    $table->string('address_line_2')->nullable();
    $table->string('town')->nullable();
    $table->string('district')->nullable();
    $table->string('state')->nullable();
    $table->string('pincode')->nullable();
    $table->string('landmark')->nullable();

    // Location fields
    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();

    $table->string('contact_person')->nullable();
    $table->string('designation_contact')->nullable();
    $table->string('contact_number')->nullable();
    $table->string('email')->nullable();
    $table->string('gst')->nullable();
    $table->date('date_of_birth')->nullable();
    $table->date('date_of_anniversary')->nullable();

    // Profile login details
    $table->string('profile_photo')->nullable();
      $table->string('login_id')->unique();
      $table->string('password');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributors');
    }
};
