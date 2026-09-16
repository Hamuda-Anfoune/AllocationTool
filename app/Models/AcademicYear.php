<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'year', 'current',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'current' => 'boolean',
    ];

    protected $primaryKey = 'year';

    public $incrementing = false;

    protected $keyType = 'string';

    #[Scope]
    protected function current(Builder $query): void
    {
        $query->where('current', true);
    }

    public static function currentYear(): string
    {
        return static::query()->current()->value('year');
    }
}
