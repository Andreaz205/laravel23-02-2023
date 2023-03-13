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
        Schema::create('accent_property_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->index()->constrained('products')->onDelete('cascade');
            $table->foreignId('accent_property_id')->index()->constrained('accent_properties')->onDelete('cascade');
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
        Schema::dropIfExists('accent_property_products');
    }
};
