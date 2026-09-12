<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaLanguageChoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'preference_id', 'language_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'field_id',
    ];

    protected $primaryKey = 'field_id';
}
