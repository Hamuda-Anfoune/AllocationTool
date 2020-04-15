<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModuleRankOrderList extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module_id', 'academic_year', 'ta_email', 'total_weight', 'did_before_weight',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'total_weight' => 0.0,
        'did_before_weight' => 0.0,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'did_before' => 'boolean', // cast to bool as it's saved in DB as integer
    ];
}
