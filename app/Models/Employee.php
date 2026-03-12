<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini
use Illuminate\Database\Eloquent\Relations\HasMany;  // Tambahkan ini
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'employee_code', 
        'full_name', 
        'department_id', 
        'shift_id', // WAJIB DITAMBAHKAN untuk sistem shift
        'registered_device_id', // Untuk Device Binding [cite: 2026-01-30]
        'qr_secret_key', 
        'is_active'
    ];

    // Relasi ke Shift (Penting untuk validasi jam pulang)
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // Nama AttendanceLog harus konsisten dengan tabelmu
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function employeeDetail()
    {
        return $this->hasOne(EmployeeDetail::class);
    }

    /**
     * Cek apakah profil karyawan sudah lengkap
     */
    public function isProfileComplete(): bool
    {
        $detail = $this->employeeDetail;
        if (!$detail) return false;

        $required = ['nik', 'birth_place', 'birth_date', 'gender', 'phone', 'address', 'join_date'];
        foreach ($required as $field) {
            if (empty($detail->$field)) return false;
        }
        return true;
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Penilaian karyawan ini (via evaluatee_id)
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'evaluatee_id');
    }
}