<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rl_recipe_diets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('recipe_id')->unsigned();
            $table->unsignedBiginteger('recipe_diet_id')->unsigned();
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->foreign('recipe_diet_id')->references('id')->on('recipe_diets')->onDelete('cascade');
            $table->timestamps();

            $table->index('recipe_id');
            $table->index('recipe_diet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rl_recipe_diets');
    }
};
