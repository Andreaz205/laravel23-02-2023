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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('delivery_date')->nullable();
            $table->integer('sum')->nullable();
            $table->integer('delivery_price')->nullable();
            $table->enum('status', ['new', 'preparing', 'agreed', 'stored', 'delivered', 'aborted', 'client_enabled', 'back', 'back_process'])->default('new');
            $table->boolean('is_payed')->default(false);
            $table->string('user_name');
            $table->string('phone');
            $table->string('email');
            $table->enum('payment_variant', ['cash', 'card', 'partials', 'out_variant']);
            $table->enum('delivery_type', ['delivery', 'pickup', 'none']);
            $table->text('client_comment')->nullable();
            $table->text('admin_comment')->nullable();
            $table->string('address');
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->onDelete('cascade');
            $table->foreignId('duty_admin_id')->nullable()->constrained('admins')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
