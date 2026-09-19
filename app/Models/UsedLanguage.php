<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedLanguage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id', 'language_id', 'priority', 'academic_year',
    ];

    protected $primaryKey = 'field_id';

    #[Scope]
    protected function withLanguageName(Builder $query): void
    {
        $query->join('languages', 'languages.language_id', '=', 'used_languages.language_id');
    }

    #[Scope]
    protected function forModuleForYear(Builder $query, string $moduleId, string $academicYear): void
    {
        $query->where('used_languages.module_id', $moduleId)
            ->where('used_languages.academic_year', $academicYear);
    }
}
