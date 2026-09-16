<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaLanguageChoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'preference_id', 'language_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'field_id',
    ];

    protected $primaryKey = 'field_id';

    #[Scope]
    protected function withLanguageName(Builder $query): void
    {
        $query->join('languages', 'languages.language_id', '=', 'ta_language_choices.language_id');
    }
}
