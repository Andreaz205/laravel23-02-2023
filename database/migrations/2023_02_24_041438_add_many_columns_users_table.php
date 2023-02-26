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
        Schema::table('users', function (Blueprint $table) {
            $table->string('jural_address')->nullable();
            $table->string('inn')->nullable();
            $table->string('phone')->nullable();
            $table->string('additional_phone')->nullable();
            $table->string('ogrn')->nullable();
            $table->string('bic')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('correspondent_account')->nullable();
            $table->string('calculated_account')->nullable();
            $table->string('unloading_address')->nullable();
            $table->boolean('is_subscribed_to_news')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(
                'jural_address',
                'inn',
                'phone',
                'ogrn',
                'additional_phone',
                'bic',
                'bank_name',
                'correspondent_account',
                'calculated_account',
                'unloading_address',
                'is_subscribed_to_news'
            );
        });
    }
};
