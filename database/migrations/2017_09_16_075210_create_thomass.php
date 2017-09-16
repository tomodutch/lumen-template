<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThomass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('thomas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('age');
            $table->timestampTz('date_of_birth');

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('thomass');
    }
}