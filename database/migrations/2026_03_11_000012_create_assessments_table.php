<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('evaluatee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('assessment_date');
            $table->enum('period_type', ['Harian', 'Mingguan', 'Bulanan']);
            $table->string('period_label'); // cth: "Maret 2026", "Minggu 2 Maret 2026"
            $table->text('general_notes')->nullable();
            $table->boolean('show_to_employee')->default(true);
            $table->timestamps();

            // 1 karyawan hanya bisa dinilai sekali per periode
            $table->unique(['evaluatee_id', 'period_type', 'period_label'], 'unique_assessment_per_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
