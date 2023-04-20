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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_variant');
//            $table->enum('payment_variant', ['cash', 'card', 'installment_tinkoff', 'other_variant']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
//            $table->dropColumn('payment_variant');
            $table->enum('payment_variant', ['cash', 'card', 'partials', 'out_variant'])->default('cash');
        });
    }
};
