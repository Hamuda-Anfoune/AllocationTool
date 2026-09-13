<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulePriorityWeight extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type', 'module_weight_1', 'module_weight_2', 'module_weight_3', 'module_weight_4', 'module_weight_5',
        'module_weight_6', 'module_weight_7', 'module_weight_8', 'module_weight_9', 'module_weight_10',
    ];
}
