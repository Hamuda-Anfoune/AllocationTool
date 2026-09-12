<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ta_allocation_data extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ta_allocation_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'allocation_id', 'ta_id', 'contact_hours','marking_hours', 'academic_year',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    protected $primaryKey = 'field_id'; // Setting PrimaryKey
}
