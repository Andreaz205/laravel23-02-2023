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
            $table->string('family')->nullable();
            $table->string('patronymic')->nullable();
            $table->string('password')->nullable()->change();
            $table->string('phone')->unique()->change();
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
            $table->dropColumn('family');
            $table->dropColumn('patronymic');
            $table->string('password')->change();
            $table->dropUnique('users_phone_unique');
        });
    }
};
