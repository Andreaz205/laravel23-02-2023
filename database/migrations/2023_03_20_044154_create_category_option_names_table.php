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
        Schema::create('category_option_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->index()->constrained('categories')->onDelete('cascade');
            $table->foreignId('option_name_id')->index()->constrained('option_names')->onDelete('cascade');
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
        Schema::dropIfExists('category_option_names');
    }
};
