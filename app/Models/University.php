<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'university_email',
    ];
}
