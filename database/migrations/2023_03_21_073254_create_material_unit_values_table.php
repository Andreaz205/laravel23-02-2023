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
        Schema::create('material_unit_values', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->foreignId('material_unit_id')->index()->constrained('material_units')->onDelete('cascade');
            $table->foreignId('parent_material_unit_value_id')->nullable()->index()->constrained('material_unit_values')->onDelete('cascade');
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
        Schema::dropIfExists('material_unit_values');
    }
};
