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
            $table->dropColumn('user_id');
            $table->dropColumn('duty_admin_id');
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
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->onDelete('cascade');
            $table->foreignId('duty_admin_id')->nullable()->constrained('admins')->onDelete('cascade');

//            $table->foreignId('user_id')->nullable()->index()->constrained('users')->onDelete('set null');
//            $table->foreignId('duty_admin_id')->nullable()->constrained('admins')->onDelete('set null');
        });
    }
};
