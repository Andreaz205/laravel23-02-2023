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
        Schema::table('order_variants', function (Blueprint $table) {
            $table->foreignId('order_variant_copy_id')->index()->constrained('order_variant_copies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_variants', function (Blueprint $table) {
            $table->dropColumn('order_variant_copy_id');
        });
    }
};
