<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model LeaveRequest
class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id', 'type', 'start_date', 'end_date', 
        'reason', 'status', 'approved_by'
    ];
}

// Model Holiday
class Holiday extends Model
{
    protected $fillable = ['holiday_date', 'name', 'is_national'];
}
