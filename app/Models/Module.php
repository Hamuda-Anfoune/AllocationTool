<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id', 'module_name', 'convenor_email', 'academic_year',
    ];

    protected $primaryKey = 'module_id';

    public $incrementing = false;

    protected $keyType = 'string';
}
