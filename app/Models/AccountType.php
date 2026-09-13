<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_type_id', 'account_type',
    ];

    protected $primaryKey = 'account_type_id';

    public $incrementing = false;

    protected $keyType = 'string';
}
