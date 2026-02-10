<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_code', 'full_name', 'department_id', 
        'registered_device_id', 'qr_secret_key', 'is_active'
    ];

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function attendances() {
        return $this->hasMany(Attendance::class);
    }

    public function schedules() {
        return $this->hasMany(Schedule::class);
    }

    public function leaveRequests() {
        return $this->hasMany(LeaveRequest::class);
    }
}
