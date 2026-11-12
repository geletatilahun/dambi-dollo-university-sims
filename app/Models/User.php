<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }
    
    public function advisees()
    {
        return $this->hasMany(Student::class, 'academic_advisor_user_id');
    }

    public function advisingNotes()
    {
        return $this->hasMany(AdvisingNote::class, 'advisor_user_id');
    }
}