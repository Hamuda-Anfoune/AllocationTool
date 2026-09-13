<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleRepetitionWeight extends Model
{
    /**
     * The table associated with the model.
     *
     * The table itself keeps its original (misspelled) name; renaming the
     * underlying table/column is a data migration concern, not a code port one.
     *
     * @var string
     */
    protected $table = 'module_repeatition_weights';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type', 'repeated_times_1', 'repeated_times_2', 'repeated_times_3', 'repeated_times_4', 'repeated_times_5',
    ];
}
