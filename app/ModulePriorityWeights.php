<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModulePriorityWeights extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'module_weight_1', 'module_weight_2', 'module_weight_3', 'module_weight_4','module_weight_5',
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
