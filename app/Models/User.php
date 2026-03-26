<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    public function lecturerProfile(): HasOne
    {
        return $this->hasOne(LecturerProfile::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'course_id')
            ->withPivot('status', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function announcementsCreated(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // =====================
    // QUERY SCOPES
    // =====================

    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }

    public function scopeLecturers($query)
    {
        return $query->role('lecturer');
    }

    public function scopeStudents($query)
    {
        return $query->role('student');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    // =====================
    // BUSINESS LOGIC
    // =====================

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isLecturer(): bool
    {
        return $this->hasRole('lecturer');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function recordFailedLogin(): void
    {
        $this->update([
            'failed_login_attempts' => $this->failed_login_attempts + 1,
            'last_failed_login_at' => now(),
        ]);
    }

    public function recordSuccessfulLogin(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'last_login_at' => now(),
        ]);
    }

    public function isLocked(): bool
    {
        return $this->failed_login_attempts >= 5 &&
            $this->last_failed_login_at->diffInMinutes() < 15;
    }
}

