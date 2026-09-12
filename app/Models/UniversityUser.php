<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniversityUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'account_type_id',
    ];

    protected $primaryKey = 'email';

    public $incrementing = false;

    protected $keyType = 'string';
}
