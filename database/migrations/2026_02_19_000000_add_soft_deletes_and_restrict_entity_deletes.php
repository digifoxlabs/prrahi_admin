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
        Schema::table('distributors', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('retailers', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropForeign(['parent_id']);
            $table->foreign('parent_id')->references('id')->on('products')->restrictOnDelete();
        });

        Schema::table('distributor_documents', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('distributor_companies', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('distributor_banks', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('distributor_godowns', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('distributor_manpowers', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('distributor_investments', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('distributor_vehicles', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->dropForeign(['retailer_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
            $table->foreign('retailer_id')->references('id')->on('retailers')->restrictOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
        });

        Schema::table('distributor_products', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('distributor_stocks', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('distributor_inventory_transactions', function (Blueprint $table) {
            $table->dropForeign('dit_dist_fk');
            $table->foreign('distributor_id', 'dit_dist_fk')
                ->references('id')
                ->on('distributors')
                ->restrictOnDelete();
        });

        Schema::table('retailer_sales', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->dropForeign(['retailer_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
            $table->foreign('retailer_id')->references('id')->on('retailers')->restrictOnDelete();
        });

        Schema::table('retail_orders', function (Blueprint $table) {
            $table->dropForeign(['retailer_id']);
            $table->dropForeign(['distributor_id']);
            $table->foreign('retailer_id')->references('id')->on('retailers')->restrictOnDelete();
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
        });

        Schema::table('retail_order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retail_order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('retail_orders', function (Blueprint $table) {
            $table->dropForeign(['retailer_id']);
            $table->dropForeign(['distributor_id']);
            $table->foreign('retailer_id')->references('id')->on('retailers')->cascadeOnDelete();
            $table->foreign('distributor_id')->references('id')->on('distributors')->nullOnDelete();
        });

        Schema::table('retailer_sales', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->dropForeign(['retailer_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
            $table->foreign('retailer_id')->references('id')->on('retailers')->cascadeOnDelete();
        });

        Schema::table('distributor_inventory_transactions', function (Blueprint $table) {
            $table->dropForeign('dit_dist_fk');
            $table->foreign('distributor_id', 'dit_dist_fk')
                ->references('id')
                ->on('distributors')
                ->cascadeOnDelete();
        });

        Schema::table('distributor_stocks', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
        });

        Schema::table('distributor_products', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->dropForeign(['retailer_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
            $table->foreign('retailer_id')->references('id')->on('retailers')->nullOnDelete();
        });

        Schema::table('distributor_vehicles', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
        });

        Schema::table('distributor_investments', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
        });

        Schema::table('distributor_manpowers', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
        });

        Schema::table('distributor_godowns', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
        });

        Schema::table('distributor_banks', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
        });

        Schema::table('distributor_companies', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
        });

        Schema::table('distributor_documents', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->foreign('parent_id')->references('id')->on('products')->nullOnDelete();
            $table->dropSoftDeletes();
        });

        Schema::table('retailers', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->nullOnDelete();
            $table->dropSoftDeletes();
        });

        Schema::table('distributors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
