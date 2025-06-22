<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'lecturer_id'
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id', 'id');
    }
}
