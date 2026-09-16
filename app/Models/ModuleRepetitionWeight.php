<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleRepetitionWeight extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type', 'repeated_times_1', 'repeated_times_2', 'repeated_times_3', 'repeated_times_4', 'repeated_times_5',
    ];
}
