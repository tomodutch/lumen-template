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
        Schema::create('thomass', function(Blueprint $table) {
                                        $table->uuid('id');
                            $table->string('title');
                            $table->int('age');
            
            $table->timestampsTz();

            $table->primary([
                                    'id',
                            ]);
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