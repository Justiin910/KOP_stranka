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
        Schema::table('products', function (Blueprint $table) {
            // Store variant pricing modifiers
            // e.g., { "storage": { "128GB": 0, "256GB": 100, "512GB": 200 }, "ram": { "12GB": 0, "16GB": 50 } }
            $table->json('variant_pricing')->nullable()->after('variants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('variant_pricing');
        });
    }
};
