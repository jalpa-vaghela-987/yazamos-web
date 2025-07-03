<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\QueryCrudBuilderTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, HasFactory, HasApiTokens, Notifiable, HasRoles, SoftDeletes, QueryCrudBuilderTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'country_code',
        'profile_photo',
        'created_by',
        'transaction_id',
        'is_paid',
        'address',
        'company_name',
        'email_verified_at',
        'google2fa_secret',
        'two_factor_verified',
        'otp',
        'otp_expires_at'
    ];
    protected $searchColumns = [
        'name',
        'email',
        'company_name',
        'google2fa_secret',
        'two_factor_verified',
        'address',
        'phone_number',
        'country_code',
        ['relationship' => 'roles', 'column' => 'name'],
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUserProjects()
    {
        return $this->hasMany(AssignedUserProject::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function fcmToken()
    {
        return $this->hasOne(FcmToken::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeFilterRecords(Builder $query, $input = [])
    {
        $filters = Arr::get($input, 'filters', []);

        foreach ($filters as $column => $value) {
            if ($column === 'roles' && !empty($value)) {
                $query->whereHas('roles', function ($rolesQuery) use ($value) {
                    if ($value !== 'all') {
                        $rolesQuery->where('name', $value);
                    }
                });
            }

            if ($column === 'project_id' && !empty($value)) {
                $query->where(function ($q) use ($value) {
                    $q->whereHas('projects', function ($subQuery) use ($value) {
                        $subQuery->where('id', $value); // User owns the project
                    })->orWhereHas('assignedUserProjects', function ($subQuery) use ($value) {
                        $subQuery->where('project_id', $value); // User is assigned to the project
                        $subQuery->where('invitation_status', 'accepted');
                    });
                });
            }
        }

        return $query;
    }
}
