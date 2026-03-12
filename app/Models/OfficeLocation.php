<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'radius_meter'];

    public function departments() {
        return $this->hasMany(Department::class, 'office_location_id');
    }
}
