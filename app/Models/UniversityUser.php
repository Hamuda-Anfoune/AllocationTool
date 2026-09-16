<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UniversityUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email', 'account_type_id',
    ];

    protected $primaryKey = 'email';

    public $incrementing = false;

    protected $keyType = 'string';

    #[Scope]
    protected function withAccountType(Builder $query): void
    {
        $query->join('account_types', 'account_types.account_type_id', '=', 'university_users.account_type_id');
    }
}
