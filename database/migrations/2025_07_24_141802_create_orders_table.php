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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('distributor_id')->constrained()->onDelete('cascade');
            $table->text('billing_address')->nullable();
             $table->foreignId('retailer_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('sgst', 10, 2)->default(0);
            $table->decimal('cgst', 10, 2)->default(0);
            $table->decimal('igst', 10, 2)->default(0);
            $table->decimal('round_off', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->boolean('bill_generated')->default(false);
            $table->text('admin_comments')->nullable();
            $table->date('order_date');   
            $table->enum('invoice_status', ['pending', 'generated'])->default('pending');
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->enum('dispatch_status', ['pending', 'dispatched', 'delivered', 'cancelled'])->default('pending');       
            $table->date('inventory_synced_at')->nullable();  
            $table->nullableMorphs('created_by'); // Adds created_by_id and created_by_type
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
