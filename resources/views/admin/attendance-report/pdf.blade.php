<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi - PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p.subtitle { text-align: center; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #4e73df; color: white; font-size: 11px; }
        tr:nth-child(even) { background-color: #f8f9fc; }
        .badge { padding: 2px 8px; border-radius: 3px; color: white; font-size: 10px; }
        .badge-success { background: #1cc88a; }
        .badge-warning { background: #f6c23e; color: #333; }
        .badge-danger { background: #e74a3b; }
        .badge-info { background: #36b9cc; }
        .badge-secondary { background: #858796; }
        @media print {
            body { margin: 0; }
            button { display: none; }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" style="margin-bottom:15px;padding:8px 20px;background:#4e73df;color:#fff;border:none;border-radius:4px;cursor:pointer;">
        🖨️ Print / Save PDF
    </button>

    <h2>Laporan Absensi Karyawan</h2>
    <p class="subtitle">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Department</th>
                <th>Shift</th>
                <th>Tanggal</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Status In</th>
                <th>Status Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $i => $att)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $att->employee->full_name ?? '-' }}</td>
                <td>{{ $att->employee->department->name ?? '-' }}</td>
                <td>{{ $att->shift->name ?? '-' }}</td>
                <td>{{ $att->work_date?->format('Y-m-d') }}</td>
                <td>{{ $att->clock_in?->format('H:i:s') ?? '-' }}</td>
                <td>{{ $att->clock_out?->format('H:i:s') ?? '-' }}</td>
                <td>
                    @php $colors = ['Ontime'=>'success','Present'=>'success','Late'=>'warning','Alpha'=>'danger','Sick'=>'info','Leave'=>'secondary']; @endphp
                    <span class="badge badge-{{ $colors[$att->status_in] ?? 'secondary' }}">{{ $att->status_in ?? '-' }}</span>
                </td>
                <td>{{ $att->status_out ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
