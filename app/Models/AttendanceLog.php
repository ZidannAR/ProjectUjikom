<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    public $timestamps = false; 
    protected $table = 'attendance_logs';
    protected $fillable = [
        'employee_id', 
        'qr_token_hash', 
        'scanned_at'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}