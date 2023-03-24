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
        Schema::table('option_values', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->foreignId('material_unit_value_id')->nullable()->index()->constrained('material_unit_values')->onDelete('cascade');
            $table->foreignId('material_unit_id')->nullable()->index()->constrained('material_units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('option_values', function (Blueprint $table) {
            $table->string('title')->change();
            $table->dropColumn('material_unit_value_id');
            $table->dropColumn('material_unit_id');
        });
    }
};
