<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'language_id', 'language_name',
    ];

    protected $primaryKey = 'language_id';

    public $incrementing = false;

    protected $keyType = 'string';
}
