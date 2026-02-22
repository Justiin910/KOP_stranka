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
        // Add variants column to products (JSON: {ram: string, storage: string, etc})
        Schema::table('products', function (Blueprint $table) {
            $table->json('variants')->nullable()->after('specs')->comment('Available variants like RAM, Storage options');
        });

        // Add variant_options to cart_items (JSON: {ram: "16GB", storage: "512GB"})
        Schema::table('cart_items', function (Blueprint $table) {
            $table->json('variant_options')->nullable()->after('quantity')->comment('Selected product variant options');
        });

        // Add variant_options to order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->json('variant_options')->nullable()->after('price')->comment('Selected product variant options');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('variants');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('variant_options');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('variant_options');
        });
    }
};
