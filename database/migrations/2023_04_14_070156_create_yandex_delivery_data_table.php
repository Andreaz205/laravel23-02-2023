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
        Schema::create('yandex_delivery_data', function (Blueprint $table) {
            $table->id();
//            $table->integer('delivery_cost')->nullable();
            $table->enum('payment_method', ["already_paid", "cash_on_receipt", "card_on_receipt", "cashless"]);
            $table->string('destination_details_comment')->nullable();
            $table->string('destination_details_full_address')->nullable();
            $table->string('destination_details_room')->nullable();
            $table->string('operator_request_id'); // id order
            $table->decimal('latitude', 8, 2);
            $table->decimal('longitude', 8, 2);

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
        Schema::dropIfExists('yandex_delivery_data');
    }
};
