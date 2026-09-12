<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'allocation_id', 'academic_year', 'module_id', 'ta_id', 'creator_email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    // protected $primaryKey = 'allocation_id'; // Setting PrimaryKey

    // public $incrementing = false; // To stop Eloquent from assuming primaryKey is auto incrementing

    // protected $keyType = 'string'; // To stop Eloquent from assuming primaryKey is an int
}
