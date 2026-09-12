<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaAllocationData extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'allocation_id', 'ta_id', 'contact_hours', 'marking_hours', 'academic_year',
    ];
}
