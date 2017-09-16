<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Thomas extends Model
{
    public $table = 'thomass';

    protected $fillable = [
                                            'id',
                                                'title',
                                                'age',
                                                'date_of_birth',
                        ];
}
