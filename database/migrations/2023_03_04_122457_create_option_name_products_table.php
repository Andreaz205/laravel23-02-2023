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
        Schema::create('option_name_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_name_id')->index()->constrained('option_names')->onDelete('cascade');
            $table->foreignId('product_id')->index()->constrained('products')->onDelete('cascade');
            $table->foreignId('default_option_value_id')->index()->constrained('option_values')->onDelete('cascade');
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
        Schema::dropIfExists('option_name_products');
    }
};
