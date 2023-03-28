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
        Schema::create('interior_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interior_id')->index()->constrained('interiors')->onDelete('cascade');
            $table->foreignId('variant_id')->index()->constrained('variants')->onDelete('cascade');
            $table->float('left')->default(0);
            $table->float('top')->default(0);
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
        Schema::dropIfExists('interior_variants');
    }
};
