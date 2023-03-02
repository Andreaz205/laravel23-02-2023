<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->integer('coefficient_conversion')->nullable();
            $table->integer('bonus_percent')->nullable();
            $table->integer('register_bonuses')->nullable();
            $table->boolean('allow_discounted')->default(false);
            $table->boolean('allow_kits')->default(false);
            $table->integer('max_discount_percents')->nullable();
            $table->enum('available_groups', ['all', 'without_groups', 'selected'])->default('all');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonuses');
    }
};
