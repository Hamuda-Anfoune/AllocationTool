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
        'type', 'repeated_times_1', 'repeated_times_2', 'repeated_times_3', 'repeated_times_4', 'repeated_times_5',
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
