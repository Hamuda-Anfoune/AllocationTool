<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsedLanguage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id', 'language_id', 'priority', 'academic_year',
    ];

    protected $primaryKey = 'field_id';
}
