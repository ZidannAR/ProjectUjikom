<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected int $row = 0;

    public function collection()
    {
        return Employee::with(['department', 'shift', 'employeeDetail'])
            ->orderBy('full_name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Karyawan',
            'Nama Lengkap',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'No. Telepon',
            'Alamat',
            'Pendidikan Terakhir',
            'Department',
            'Shift',
            'Tanggal Mulai Kerja',
            'Status Aktif',
        ];
    }

    public function map($employee): array
    {
        $this->row++;
        $detail = $employee->employeeDetail;

        return [
            $this->row,
            $employee->employee_code,
            $employee->full_name,
            $detail->nik ?? '-',
            $detail->birth_place ?? '-',
            $detail?->birth_date?->format('Y-m-d') ?? '-',
            $detail->gender ?? '-',
            $detail->phone ?? '-',
            $detail->address ?? '-',
            $detail->last_education ?? '-',
            $employee->department->name ?? '-',
            $employee->shift->name ?? '-',
            $detail?->join_date?->format('Y-m-d') ?? '-',
            $employee->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4E73DF'],
                ],
            ],
        ];
    }
}
