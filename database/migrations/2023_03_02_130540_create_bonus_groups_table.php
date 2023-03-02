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
        Schema::create('bonus_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->index()->constrained('groups')->onDelete('cascade');
            $table->foreignId('bonus_id')->index()->constrained('bonuses')->onDelete('cascade');

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
        Schema::dropIfExists('bonus_groups');
    }
};