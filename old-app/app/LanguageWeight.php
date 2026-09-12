<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LanguageWeight extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'language_weight_1', 'language_weight_2', 'language_weight_3', 'language_weight_4','language_weight_5',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'preference_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];
}
