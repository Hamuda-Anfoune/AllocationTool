<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaPreference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'preference_id', 'ta_email', 'max_contact_hours', 'max_marking_hours', 'max_modules', 'academic_year', 'have_tier4_visa',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'have_tier4_visa' => 'boolean',
    ];

    protected $primaryKey = 'preference_id';

    public $incrementing = false;

    protected $keyType = 'string';
}
