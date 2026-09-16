<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaPreference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'preference_id', 'ta_email', 'max_contact_hours', 'max_marking_hours', 'max_modules', 'academic_year', 'have_tier4_visa',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'have_tier4_visa' => 'boolean',
    ];

    protected $primaryKey = 'preference_id';

    public $incrementing = false;

    protected $keyType = 'string';

    #[Scope]
    protected function forYear(Builder $query, string $academicYear): void
    {
        $query->where('academic_year', $academicYear);
    }
}
