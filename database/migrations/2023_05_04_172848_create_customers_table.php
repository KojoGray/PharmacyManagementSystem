<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customerFirstName');
            $table->string('customerLastName');
            $table->string('customerEmail')->unique();
            $table->string('customerGender');
            $table->date('customerBirthDate');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('customerContact');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
