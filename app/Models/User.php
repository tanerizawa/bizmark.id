<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Observers\AdminAuditObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Ai\Concerns\HasConversations;
use NotificationChannels\WebPush\HasPushSubscriptions;

#[ObservedBy([AdminAuditObserver::class])]
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasConversations, HasFactory, HasPushSubscriptions, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'full_name',
        'email',
        'position',
        'department',
        'employee_id',
        'phone',
        'notes',
        'bio',
        'expertise',
        'linkedin_url',
        'twitter_url',
        'job_title',
        'password',
        'role_id',
        'is_active',
        'avatar',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the role of the user
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames)
    {
        return $this->role && in_array($this->role->name, $roleNames);
    }

    /**
     * Check if user has a specific permission
     * Rename to hasPermission to avoid conflict with Laravel's can() method
     */
    public function hasPermission($permissionName)
    {
        return $this->role && $this->role->hasPermission($permissionName);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access recruitment module
     */
    public function canAccessRecruitment()
    {
        // Admin always has access
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->hasAnyPermission([
            'recruitment.view_jobs',
            'recruitment.manage_jobs',
            'recruitment.view_applications',
            'recruitment.process_applications',
        ]);
    }

    /**
     * Check if user can access email management
     */
    public function canAccessEmailManagement()
    {
        // Admin always has access
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->hasAnyPermission([
            'email.view_inbox',
            'email.send_email',
            'email.manage_accounts',
            'email.manage_campaigns',
            'email.manage_subscribers',
            'email.manage_templates',
            'email.manage_settings',
        ]);
    }

    /**
     * Check if user can access master data
     */
    public function canAccessMasterData()
    {
        // Admin always has access
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->hasAnyPermission([
            'master_data.view',
            'master_data.edit_permit_types',
            'master_data.edit_permit_templates',
            'finances.manage_accounts', // Cash accounts included in master data
        ]);
    }

    /**
     * Override Laravel's can() method to check custom permissions
     *
     * @param  mixed  $abilities
     * @param  array|mixed  $arguments
     */
    public function can($abilities, $arguments = []): bool
    {
        // Admin role bypasses all permission checks
        if ($this->hasRole('admin')) {
            return true;
        }

        // If it's a string, check our custom permission system
        if (is_string($abilities)) {
            return $this->hasPermission($abilities);
        }

        // Otherwise, fall back to parent implementation
        return parent::can($abilities, $arguments);
    }

    /**
     * Get the tasks assigned to this user
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
    }

    /**
     * Alias for assignedTasks (for dashboard compatibility)
     */
    public function tasks()
    {
        return $this->assignedTasks();
    }

    /**
     * Email accounts assigned to this user
     */
    public function emailAccounts()
    {
        return $this->belongsToMany(
            EmailAccount::class,
            'email_assignments',
            'user_id',
            'email_account_id'
        )->withPivot('can_read', 'can_send', 'can_delete', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Email assignments for this user
     */
    public function emailAssignments()
    {
        return $this->hasMany(EmailAssignment::class, 'user_id');
    }

    /**
     * Articles authored by this user
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * Get display name for public author attribution
     */
    public function getAuthorDisplayNameAttribute(): string
    {
        return $this->full_name ?: $this->name;
    }

    /**
     * Get expertise as array
     */
    public function getExpertiseListAttribute(): array
    {
        if (empty($this->expertise)) {
            return [];
        }

        return array_map('trim', explode(',', $this->expertise));
    }
}
