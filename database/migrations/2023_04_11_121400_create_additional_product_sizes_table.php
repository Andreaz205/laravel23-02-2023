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
        Schema::create('additional_product_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->integer('length');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->foreignId('product_id')->index()->constrained('products')->onDelete('cascade');
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
        Schema::dropIfExists('additional_product_sizes');
    }
};
