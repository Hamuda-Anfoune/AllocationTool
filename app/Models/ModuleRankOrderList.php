<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleRankOrderList extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'academic_year', 'module_id', 'ta_email', 'ta_total_weight', 'did_before_weight',
        'module_priority_for_ta', 'module_priority_for_ta_weight', 'languages_similarity_weight',
    ];
}
