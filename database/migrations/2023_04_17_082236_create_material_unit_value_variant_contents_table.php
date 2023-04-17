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
        Schema::create('material_unit_value_variant_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_content_id')->constrained('variant_contents')->onDelete('cascade');
            $table->foreignId('material_unit_value_id')->constrained('material_unit_values')->onDelete('cascade');
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
        Schema::dropIfExists('material_unit_value_variant_contents');
    }
};
