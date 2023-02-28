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
        Schema::table('groups', function (Blueprint $table) {
            $table->text('discount_description')->nullable();
            $table->boolean('allow_discounted')->default(true);
            $table->boolean('allow_kits')->default(true);
            $table->boolean('is_all_categories')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['discount_description', 'allow_discounted', 'allow_kits', 'is_all_categories']);
        });
    }
};
