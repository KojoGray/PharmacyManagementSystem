<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
       
            $table->string('reminderMessage');
            $table->unsignedBigInteger('dosageId');

            $table->foreign('dosageId')
            ->references('id')
            ->on('dosages')
            ->onDelete('cascade');
            $table->unsignedBigInteger('customerId');

            $table->foreign('customerId')
            ->references('id')
            ->on('customers')
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
        Schema::dropIfExists('reminders');
    }
}
