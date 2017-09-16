<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('groups', function(Blueprint $table) {
                                        $table->uuid('id') ;
                            $table->string('name') ;
                            $table->uuid('account_id') ;
            
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
        Schema::drop('groups');
    }
}