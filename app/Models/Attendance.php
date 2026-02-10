<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance'; // Karena kita tidak pakai jamak 'attendances' di migration
    protected $fillable = [
        'employee_id', 'shift_id', 'work_date', 'clock_in', 'clock_out',
        'status_in', 'status_out', 'gps_latitude', 'gps_longitude', 'device_id_used'
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in'  => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function shift() {
        return $this->belongsTo(Shift::class);
    }
}
