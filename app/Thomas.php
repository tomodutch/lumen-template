<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thomas extends Model
{
    public $table = 'thomass';

    use SoftDeletes;

    protected $fillable = [
        'title',
        'date_of_birth',
        'age',
    ];

    protected $dates = [
        'deleted_at',
        'date_of_birth',
    ];
}
