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
        Schema::create('related_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_variant_id')->index()->constrained('variants')->onDelete('cascade');
            $table->foreignId('related_variant_id')->index()->constrained('variants')->onDelete('cascade');
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
        Schema::dropIfExists('related_variants');
    }
};
