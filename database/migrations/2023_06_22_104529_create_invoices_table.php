<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger("customer_id");
            $table->foreign('customer_id')->references('id')->on('customers')->ondelete('cascade');
            $table->unsignedBigInteger('medication_id');
            $table->foreign('medication_id')->references('id')->on('medications')->ondelete('cascade');;
            $table->unsignedBigInteger('sales_id');
            $table->foreign('sales_id')->references('id')->on('sales')->ondelete('cascade');
            $table->unsignedBigInteger('unit_quantity');
            
            $table->double('unit_price');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
