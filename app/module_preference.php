<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class module_preference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field_id', 'module_id', 'convenor_user_id', 'no_of_assistants', 'no_of_contact_hours', 'no_of_marking_hours', 'academic_year', 'semester',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'convenor_user_id', 'academic_year', 'semester',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    protected $primaryKey = 'field_id'; // Setting PrimaryKey

    // public $incrementing = false; // To stop Eloquent from assuming primaryKey is auto incrementing

    // protected $keyType = 'string'; // To stop Eloquent from assuming primaryKey is an int

}
