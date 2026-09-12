<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsedLanguage extends Model
{
    /**
     * The table associated with the model.
     *
     * The table itself keeps its original (misspelled) name; renaming the
     * underlying table/column is a data migration concern, not a code port one.
     *
     * @var string
     */
    protected $table = 'used_langauges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module_id', 'language_id', 'priority', 'academic_year',
    ];

    protected $primaryKey = 'field_id';
}
