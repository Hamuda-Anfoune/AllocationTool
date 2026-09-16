<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'account_type_id', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const array TA_ACCOUNT_TYPE_IDS = ['003', '004'];

    public const array ADMIN_ACCOUNT_TYPE_IDS = ['000', '001'];

    public const string CONVENOR_ACCOUNT_TYPE_ID = '002';

    protected $primaryKey = 'email';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->account_type_id === '000';
    }

    public function isAdmin(): bool
    {
        return in_array($this->account_type_id, self::ADMIN_ACCOUNT_TYPE_IDS, true);
    }

    public function isConvenor(): bool
    {
        return $this->account_type_id === self::CONVENOR_ACCOUNT_TYPE_ID;
    }

    public function isTaOrGta(): bool
    {
        return in_array($this->account_type_id, self::TA_ACCOUNT_TYPE_IDS, true);
    }

    /**
     * @return HasMany<TaPreference, $this>
     */
    public function taPreferences(): HasMany
    {
        return $this->hasMany(TaPreference::class, 'ta_email', 'email');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('users.active', true);
    }

    #[Scope]
    protected function tasAndGtas(Builder $query): void
    {
        $query->whereIn('users.account_type_id', self::TA_ACCOUNT_TYPE_IDS);
    }

    #[Scope]
    protected function admins(Builder $query): void
    {
        $query->whereIn('users.account_type_id', self::ADMIN_ACCOUNT_TYPE_IDS);
    }

    #[Scope]
    protected function convenors(Builder $query): void
    {
        $query->where('users.account_type_id', self::CONVENOR_ACCOUNT_TYPE_ID);
    }

    #[Scope]
    protected function withAccountType(Builder $query): void
    {
        $query->join('account_types', 'account_types.account_type_id', '=', 'users.account_type_id');
    }

    #[Scope]
    protected function withoutTaPreferencesForYear(Builder $query, string $academicYear): void
    {
        $query->whereNotExists(function ($query) use ($academicYear) {
            $query->select(DB::raw(1))
                ->from('ta_preferences')
                ->whereColumn('ta_preferences.ta_email', 'users.email')
                ->where('academic_year', $academicYear);
        });
    }
}
