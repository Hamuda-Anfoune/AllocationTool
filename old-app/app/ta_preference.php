<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ta_preference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ta_email', 'max_contact_hours', 'max_marking_hours', 'max_modules', 'academic_year', 'have_tier4_visa',
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
        'have_tier4_visa' => 'boolean', // cast to bool as it's saved in DB as integer
    ];

    protected $primaryKey = 'ta_email'; // Setting PrimaryKey

    public $incrementing = false; // To stop Eloquent from assuming primaryKey is auto incrementing

    protected $keyType = 'string'; // To stop Eloquent from assuming primaryKey is an int
}
