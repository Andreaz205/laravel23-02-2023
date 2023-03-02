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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['accumulative', 'order', 'group', 'coupon']);
            $table->integer('value');
            $table->boolean('allow_discounted')->default(true);
            $table->boolean('allow_kits')->default(true);
            $table->integer('threshold')->nullable();
            $table->enum('available_groups', ['all', 'without_groups', 'selected'])->default('all');
            $table->boolean('is_all_categories')->default(true);
            $table->text('description')->nullable();
            $table->enum('coupon_type', ['disposable', 'reusable'])->nullable();
            $table->date('deadline')->nullable();
            $table->integer('used_count')->nullable();
            $table->string('coupon_code')->nullable();
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
        Schema::dropIfExists('discounts');
    }
};
