<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'actor_type', 'actor_id', 'actor_name',
        'action', 'subject_type', 'subject_id',
        'description', 'meta', 'ip_address',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Record an admin action.
     */
    public static function recordAdmin(
        string $action,
        string $description,
        ?Model $subject = null,
        array  $meta    = []
    ): void {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return;

        static::create([
            'actor_type'   => 'admin',
            'actor_id'     => $admin->id,
            'actor_name'   => $admin->name,
            'action'       => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id'   => $subject?->id,
            'description'  => $description,
            'meta'         => $meta ?: null,
            'ip_address'   => Request::ip(),
        ]);
    }

    /**
     * Record a student action.
     */
    public static function recordStudent(
        string $action,
        string $description,
        ?Model $subject = null,
        array  $meta    = []
    ): void {
        $student = Auth::guard('student')->user();
        if (!$student) return;

        static::create([
            'actor_type'   => 'student',
            'actor_id'     => $student->id,
            'actor_name'   => $student->full_name,
            'action'       => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id'   => $subject?->id,
            'description'  => $description,
            'meta'         => $meta ?: null,
            'ip_address'   => Request::ip(),
        ]);
    }

    public function getActionIconAttribute(): string
    {
        return match (true) {
            str_contains($this->action, 'enrollment') => 'bi-journal-check',
            str_contains($this->action, 'payment')    => 'bi-credit-card',
            str_contains($this->action, 'grade')      => 'bi-mortarboard',
            str_contains($this->action, 'login')      => 'bi-door-open',
            str_contains($this->action, 'document')   => 'bi-file-earmark',
            str_contains($this->action, 'setting')    => 'bi-gear',
            default                                   => 'bi-activity',
        };
    }
}
