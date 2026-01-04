<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('brand');
            $table->decimal('price', 10, 2);
            $table->decimal('oldPrice', 10, 2)->nullable();
            $table->string('image');
            $table->float('rating')->default(0);
            $table->integer('reviews')->default(0);
            $table->string('category'); // phones, notebooks, tablets...
            $table->integer('stock')->default(0);
            $table->string('monthlyPrice')->nullable();
            $table->text('description')->nullable();
            $table->string('service')->nullable();
            $table->string('topBadge')->nullable();
            $table->timestamp('addedAt')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
