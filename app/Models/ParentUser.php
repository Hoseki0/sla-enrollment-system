<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ParentUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'parents';

    protected $fillable = [
        'name', 'email', 'contact_number', 'relationship', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $first = $parts[0][0] ?? 'P';
        $last  = $parts[count($parts) - 1][0] ?? '';
        return strtoupper($first . ($last !== $first ? $last : ''));
    }
}
