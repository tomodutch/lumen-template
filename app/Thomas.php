<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Thomas extends Model
{
    public $table = 'thomass';

    protected $fillable = [
                                                                'title',
                                                'date_of_birth',
                                                'age',
                        ];

    protected $dates = [
                                                                            'date_of_birth',
                                            ];
}
