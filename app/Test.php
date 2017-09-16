<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
            protected $primaryKey = 'id';
    
    protected $fillable = [
                                                                'title',
                        ];
}
