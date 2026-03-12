<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EmployeeDetail extends Model
{
    protected $fillable = [
        'employee_id', 'nik', 'birth_place', 'birth_date',
        'gender', 'phone', 'address', 'photo',
        'last_education', 'join_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'join_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Accessor: URL foto profil
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) return null;
        return Storage::url($this->photo);
    }
}
