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
        Schema::create('price_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_id')->index()->constrained('prices')->onDelete('cascade');
            $table->foreignId('variant_id')->index()->constrained('variants')->onDelete('cascade');
            $table->integer('price')->default(0);
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
        Schema::dropIfExists('price_variants');
    }
};
