<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDosagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dosages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger("medication_id");
            $table->unsignedBigInteger("AgeFrom");
            $table->unsignedBigInteger("AgeTo");
            $table->string("ageCategory");
            $table->string("dosageStrength");
            $table->text("dosageInstruction");
            $table->foreign("medication_id")->references("id")->on("medications")->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dosages');
    }
}
