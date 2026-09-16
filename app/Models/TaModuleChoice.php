<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaModuleChoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'preference_id', 'ta_email', 'module_id', 'priority', 'did_before',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'field_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'did_before' => 'boolean',
    ];

    protected $primaryKey = 'field_id';

    #[Scope]
    protected function withModuleName(Builder $query): void
    {
        $query->join('modules', 'modules.module_id', '=', 'ta_module_choices.module_id');
    }
}
