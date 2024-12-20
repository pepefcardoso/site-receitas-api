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
        Schema::create('rl_post_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('post_id')->unsigned();
            $table->unsignedBiginteger('post_category_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('post_category_id')->references('id')->on('post_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rl_post_categories');
    }
};
