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
        Schema::create('order_field_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->index()->constrained('orders')->onDelete('cascade');
            $table->foreignId('order_field_id')->index()->constrained('order_fields')->onDelete('cascade');
            $table->string('value', 255)->nullable();
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
        Schema::dropIfExists('order_field_orders');
    }
};
