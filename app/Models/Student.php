<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'student_id', 'lrn', 'last_name', 'first_name', 'middle_name', 'suffix',
        'birthdate', 'gender', 'civil_status', 'religion', 'nationality',
        'email', 'contact_number', 'address', 'barangay', 'municipality', 'province', 'zip_code',
        'father_name', 'father_occupation', 'father_contact',
        'mother_name', 'mother_occupation', 'mother_contact',
        'guardian_name', 'guardian_relationship', 'guardian_contact',
        'last_school_attended', 'last_school_address', 'school_type',
        'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'birthdate' => 'date',
        'password'  => 'hashed',
    ];

    // Auth username field — login with student_id
    public function username(): string
    {
        return 'student_id';
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function latestEnrollment()
    {
        return $this->hasOne(Enrollment::class)->latestOfMany();
    }

    public function getFullNameAttribute(): string
    {
        return $this->last_name . ', ' . $this->first_name
            . ($this->middle_name ? ' ' . $this->middle_name : '')
            . ($this->suffix ? ' ' . $this->suffix : '');
    }

    public function getAgeAttribute(): int
    {
        return $this->birthdate ? $this->birthdate->age : 0;
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public static function generateStudentId(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
