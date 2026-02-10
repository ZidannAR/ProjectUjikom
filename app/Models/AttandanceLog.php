<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttandanceLog extends Model
{
    // Karena kita tidak butuh created_at & updated_at standar
    public $timestamps = false; 

    protected $fillable = [
        'employee_id', 
        'qr_token_hash', 
        'scanned_at'
    ];
}