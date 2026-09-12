<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaModuleChoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'preference_id', 'ta_email', 'module_id', 'priority', 'did_before',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'preference_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'did_before' => 'boolean',
    ];

    protected $primaryKey = 'field_id';
}
