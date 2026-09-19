<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id', 'module_name', 'convenor_email', 'academic_year',
    ];

    protected $primaryKey = 'module_id';

    public $incrementing = false;

    protected $keyType = 'string';

    #[Scope]
    protected function withoutPreferencesForYear(Builder $query, string $academicYear): void
    {
        $query->whereNotExists(function ($query) use ($academicYear) {
            $query->select(DB::raw(1))
                ->from('module_preferences')
                ->whereColumn('module_preferences.module_id', 'modules.module_id')
                ->where('academic_year', $academicYear);
        });
    }
}
