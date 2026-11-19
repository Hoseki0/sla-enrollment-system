<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    protected $fillable = [
        'request_no', 'student_id', 'enrollment_id', 'document_type',
        'other_document', 'purpose', 'copies', 'status',
        'admin_remarks', 'handled_by', 'ready_at', 'released_at',
    ];

    protected $casts = [
        'ready_at'    => 'datetime',
        'released_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function handledBy()
    {
        return $this->belongsTo(Admin::class, 'handled_by');
    }

    public static function generateRequestNo(): string
    {
        return 'DOC-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    }

    public function getDocumentLabelAttribute(): string
    {
        return $this->document_type === 'Other'
            ? ($this->other_document ?: 'Other')
            : $this->document_type;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'ready'      => 'badge-enrolled',
            'released'   => 'bg-primary text-white',
            'processing' => 'badge-jhs',
            'cancelled'  => 'badge-cancelled',
            default      => 'badge-pending',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'ready'      => 'bi-check-circle-fill',
            'released'   => 'bi-box-seam',
            'processing' => 'bi-arrow-repeat',
            'cancelled'  => 'bi-x-circle-fill',
            default      => 'bi-hourglass-split',
        };
    }
}
