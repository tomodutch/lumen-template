<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    public $table = 'groups';

    use SoftDeletes;


    protected $fillable = [
        'name',
        'description',
    ];

    protected $dates = [
        'deleted_at',
    ];
}
