<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['name', 'start_time', 'end_time', 'is_cross_day'];

    protected $casts = [
        'is_cross_day' => 'boolean',
    ];

    public function employees() {
        return $this->hasMany(Employee::class);
    }
}
