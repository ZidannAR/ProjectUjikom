<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['employee_id', 'shift_id', 'day_of_week'];

    public function shift() {
        return $this->belongsTo(Shift::class);
    }
}
