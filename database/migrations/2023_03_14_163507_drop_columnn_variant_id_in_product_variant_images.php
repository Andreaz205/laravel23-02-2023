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
        Schema::table('product_variant_images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_variant_images', function (Blueprint $table) {
            $table->foreignId('variant_id')->index()->nullable()->constrained('variants')->onDelete('cascade');
        });
    }
};