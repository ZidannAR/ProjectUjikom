<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id', 'type', 'start_date', 'end_date',
        'reason', 'status', 'approved_by',
        'attachment', 'attachment_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Cek apakah jenis cuti wajib bukti
     */
    public static function requiresAttachment(string $type): bool
    {
        return array_key_exists($type, config('leave_attachment', []));
    }

    /**
     * Ambil nama bukti yang dibutuhkan
     */
    public static function getAttachmentLabel(string $type): ?string
    {
        return config('leave_attachment.' . $type);
    }
}
