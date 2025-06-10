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
        Schema::create('pizza_ingredients', function (Blueprint $table) {
            $table->string('pizza_type_id');
            $table->integer('ingredient_id');
            $table->timestamps();

            $table->index(['pizza_type_id', 'ingredient_id'], 'pizza_type_ingredient_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pizza_ingredients');
    }
};
