<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ta_language_choice extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field_id', 'preference_id', 'language_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    protected $primaryKey = 'field_id'; // Setting PrimaryKey
}
