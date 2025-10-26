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
        Schema::create('sales_persons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('headquarter')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('town')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('phone')->nullable();
            $table->string('official_email')->nullable();
            $table->string('personal_email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_anniversary')->nullable();
            $table->string('zone')->nullable();
            $table->text('state_covered')->nullable();
            $table->text('district_covered')->nullable();
            $table->text('town_covered')->nullable();

            // New fields
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
        Schema::dropIfExists('sales_persons');
    }
};
