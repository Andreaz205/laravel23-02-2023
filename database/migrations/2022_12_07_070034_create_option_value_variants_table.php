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
        Schema::create('option_value_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->index()->constrained('variants')->onDelete('cascade');
            $table->foreignId('option_value_id')->index()->constrained('option_values')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('option_value_variants');
    }
};
