<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulePreference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module_id', 'no_of_assistants', 'no_of_contact_hours', 'no_of_marking_hours', 'academic_year',
    ];

    protected $primaryKey = 'field_id';
}
