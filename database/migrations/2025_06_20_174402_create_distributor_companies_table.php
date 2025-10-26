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
        Schema::create('distributor_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->constrained()->onDelete('cascade');

            $table->string('company_name');
            $table->string('segment')->nullable();
            $table->string('brand_name')->nullable();
            $table->text('products')->nullable();
            
            // ENUM for Working As (Super Stockist/Distributor)
            //$table->enum('working_as', ['ss', 'dist'])->nullable()->comment('SS = Super Stockist, DIST = Distributor');

            $table->string('working_as')->nullable();
            $table->string('margin')->nullable();
            $table->string('payment_terms')->nullable();
            $table->year('working_since')->nullable();
            $table->string('area_operation')->nullable();
            $table->string('monthly_to')->nullable();
            $table->string('dsr_no')->nullable();

            $table->text('details')->nullable(); // optional extra details
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_companies');
    }
};
