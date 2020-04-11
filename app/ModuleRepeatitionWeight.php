<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModuleRepeatitionWeight extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', '1_time_weight', '2_time_weight', '3_time_weight', '4_time_weight', '5_time_weight',
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
