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
        Schema::create('material_unit_value_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_unit_value_id')->index()->constrained('material_unit_values')->onDelete('cascade');
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
        Schema::dropIfExists('material_unit_value_variants');
    }
};
