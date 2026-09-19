<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePreference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id', 'no_of_assistants', 'no_of_contact_hours', 'no_of_marking_hours', 'academic_year',
    ];

    protected $primaryKey = 'field_id';

    #[Scope]
    protected function joinModule(Builder $query): void
    {
        $query->join('modules', 'modules.module_id', '=', 'module_preferences.module_id');
    }

    #[Scope]
    protected function forYear(Builder $query, string $academicYear): void
    {
        $query->where('module_preferences.academic_year', $academicYear);
    }
}
