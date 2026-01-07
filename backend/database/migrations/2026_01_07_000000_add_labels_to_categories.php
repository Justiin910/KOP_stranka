<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add nullable JSON 'labels' column to store per-locale labels, e.g. {"sk": "...", "en": "..."}
        Schema::table('categories', function (Blueprint $table) {
            $table->json('labels')->nullable()->after('label');
        });

        // Backfill existing categories into 'labels' as the 'sk' locale
        try {
            $categories = DB::table('categories')->get();
            foreach ($categories as $cat) {
                DB::table('categories')->where('id', $cat->id)->update([
                    'labels' => json_encode(['sk' => $cat->label]),
                ]);
            }
        } catch (\Throwable $e) {
            // ignore failures during schema migration backfill
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('labels');
        });
    }
};
