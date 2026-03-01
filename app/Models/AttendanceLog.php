<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    // Karena kita tidak butuh created_at & updated_at standar
    public $timestamps = false; 
    protected $table = 'attendance_logs';
    protected $fillable = [
        'employee_id', 
        'qr_token_hash', 
        'scanned_at'
    ];
}