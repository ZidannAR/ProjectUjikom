<?php

namespace Database\Seeders;

use App\Models\AssessmentCategory;
use Illuminate\Database\Seeder;

class AssessmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Disiplin',        'description' => 'Kehadiran & ketepatan waktu kerja'],
            ['name' => 'Kerja Sama Tim',  'description' => 'Kemampuan bekerja dalam tim dengan efektif'],
            ['name' => 'Inisiatif',       'description' => 'Kemampuan berinisiatif tanpa harus diperintah'],
            ['name' => 'Komunikasi',      'description' => 'Kemampuan komunikasi verbal dan tulisan'],
            ['name' => 'Tanggung Jawab',  'description' => 'Penyelesaian tugas sesuai target dan tanggung jawab'],
        ];

        foreach ($categories as $cat) {
            AssessmentCategory::updateOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, ['type' => 'Employee', 'is_active' => true])
            );
        }
    }
}
