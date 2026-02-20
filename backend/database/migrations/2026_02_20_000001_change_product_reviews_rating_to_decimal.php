<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add decimal column, copy values, drop old int column, rename
        DB::statement("ALTER TABLE product_reviews ADD COLUMN rating_decimal DECIMAL(2,1) NOT NULL DEFAULT 0");
        DB::statement("UPDATE product_reviews SET rating_decimal = rating");
        DB::statement("ALTER TABLE product_reviews DROP COLUMN rating");
        DB::statement("ALTER TABLE product_reviews CHANGE rating_decimal rating DECIMAL(2,1) NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE product_reviews ADD COLUMN rating_int TINYINT UNSIGNED NOT NULL DEFAULT 0");
        DB::statement("UPDATE product_reviews SET rating_int = ROUND(rating)");
        DB::statement("ALTER TABLE product_reviews DROP COLUMN rating");
        DB::statement("ALTER TABLE product_reviews CHANGE rating_int rating TINYINT UNSIGNED NOT NULL DEFAULT 0");
    }
};
