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
        Schema::create('rich_contents', function (Blueprint $table) {
            $table->id();
            $table->longText('json')->nullable();
            $table->longText('html')->nullable();
            $table->longText('ozon_code')->nullable();
            $table->foreignId('variant_id')->index()->constrained('variants')->onDelete('cascade');
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
        Schema::dropIfExists('rich_contents');
    }
};
