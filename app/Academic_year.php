<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Academic_year extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field_id', 'year', 'current',
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
        // 'did_before' => 'boolean', // cast to bool as it's saved in DB as integer
    ];

    // protected $primaryKey = 'field_id'; // Setting PrimaryKey

    // public $incrementing = false; // To stop Eloquent from assuming primaryKey is auto incrementing

    // protected $keyType = 'string'; // To stop Eloquent from assuming primaryKey is an int

}
