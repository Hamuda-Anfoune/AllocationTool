<?php

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'year',
        'start_date',
        'end_date',
        'is_current_year'
    ];

    protected $date = ['start_date', 'end_date'];    
}