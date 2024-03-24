<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
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
            $table->timestamps();
            $table->date('orderDate');
            $table->integer('orderQuantity');
            $table->unsignedBigInteger('supplierId');
            $table->foreign('supplierId')
            ->references('id')
            ->on('suppliers')
            ->onDelete('cascade');
            $table->unsignedBigInteger('medicationId');
            $table->foreign('medicationId')
            ->references('id')
            ->on('medications')
            ->onDelete('cascade');
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
}
