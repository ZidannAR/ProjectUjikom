<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'evaluator_id',
        'evaluatee_id',
        'assessment_date',
        'period_type',
        'period_label',
        'general_notes',
        'show_to_employee',
    ];

    protected function casts(): array
    {
        return [
            'show_to_employee' => 'boolean',
            'assessment_date'  => 'date',
        ];
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluatee()
    {
        return $this->belongsTo(Employee::class, 'evaluatee_id');
    }

    public function details()
    {
        return $this->hasMany(AssessmentDetail::class, 'assessment_id');
    }

    /**
     * Rata-rata score dari semua details
     */
    public function getAverageScoreAttribute(): float
    {
        $avg = $this->details()->avg('score');
        return round($avg ?? 0, 2);
    }

    /**
     * Label warna berdasarkan rata-rata
     */
    public function getScoreColorAttribute(): string
    {
        $avg = $this->average_score;
        if ($avg >= 4.5) return 'success';
        if ($avg >= 3.5) return 'primary';
        if ($avg >= 2.5) return 'warning';
        return 'danger';
    }
}
