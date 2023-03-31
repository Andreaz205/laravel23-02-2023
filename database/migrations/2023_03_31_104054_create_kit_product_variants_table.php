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
        Schema::create('kit_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_product_pivot_id')->index()->constrained('kit_products')->onDelete('cascade');
            $table->foreignId('variant_id')->index()->constrained('variants')->onDelete('cascade');
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
        Schema::dropIfExists('kit_product_variants');
    }
};
