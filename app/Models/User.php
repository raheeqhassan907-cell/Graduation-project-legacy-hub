<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'profile_image', 'student_id', 'job_title', 'company', 'expertise', 'graduation_year', 'professor_id', 'title', 'department', 'phone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
        ];
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isGraduate(): bool
    {
        return $this->role === 'graduate';
    }

    public function isProfessor(): bool
    {
        return $this->role === 'professor';
    }

    // Relationships
    public function projects()
    {
        return $this->hasMany(Project::class, 'graduate_id');
    }

    public function supervisedProjects()
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'user_id');
    }

    public function contributedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id')->withTimestamps();
    }
}
