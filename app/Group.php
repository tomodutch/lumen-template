<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Group extends Model
{
    public $table = 'groups';

            use SoftDeletes;
    
            public $incrementing = false;
    
    protected $fillable = [
                                                                'name',
                                                'account_id',
                        ];

    protected $dates = [
                    'deleted_at',
        
                                                                        ];
        /**
    * Boot function from laravel.
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
                            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
                    });
    }
    }
