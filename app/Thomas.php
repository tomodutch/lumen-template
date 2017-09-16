<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Thomas extends Model
{
    protected $fillable = [
        'title',
        'age',
        'date_of_birth',
    ];
}
