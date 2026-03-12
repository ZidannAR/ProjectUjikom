<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                  ->unique()
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('nik', 16)->unique()->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('phone', 15)->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->string('last_education')->nullable();
            $table->date('join_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_details');
    }
};
