<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['password' => 'hashed'];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'encoded_by');
    }

    public function getRoleLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->role));
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
