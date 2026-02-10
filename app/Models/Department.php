<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'office_location_id'];

    public function officeLocation() {
        return $this->belongsTo(OfficeLocation::class);
    }
}
