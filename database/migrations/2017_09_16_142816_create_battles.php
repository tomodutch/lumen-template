<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBattles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('battles', function(Blueprint $table) {
                                        $table->uuid('id') ;
                            $table->string('title') ;
                            $table->text('body') ;
                            $table->boolean('is_featured') ->nullable() ;
            
            $table->timestampsTz();

                            $table->primary([
                                    'id',
                                ]);
            
                            $table->softDeletes();
                    });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('battles');
    }
}