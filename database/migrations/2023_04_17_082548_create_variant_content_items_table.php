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
        Schema::create('variant_content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_content_id')->constrained('variant_contents')->onDelete('cascade');
            $table->string('image_url')->nullable();
            $table->string('image_path')->nullable();
            $table->string('description');
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
        Schema::dropIfExists('variant_content_items');
    }
};
