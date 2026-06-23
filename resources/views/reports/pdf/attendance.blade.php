<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi {{ $period }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 4px; }
        h2 { font-size: 11px; text-align: center; color: #666; margin-top: 0; margin-bottom: 20px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; border-bottom: 2px solid #d1d5db; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        .employee-header { background: #eef2ff; font-weight: bold; padding: 6px 8px; margin-top: 12px; border-radius: 4px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total { font-weight: bold; background: #f9fafb; }
        .footer { text-align: center; color: #9ca3af; font-size: 8px; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Rekap Absensi</h1>
    <h2>{{ $companyName }} — {{ $period }}</h2>

    @foreach ($grouped as $empId => $rows)
        @php $emp = $rows->first()->employee; @endphp
        <div class="employee-header">{{ $emp->name }} ({{ $emp->payment_type_label }})</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th class="text-right">Jam Kerja</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $a)
                    <tr>
                        <td>{{ $a->clock_in->format('d M Y') }}</td>
                        <td>{{ $a->clock_in->format('H:i') }}</td>
                        <td>{{ $a->clock_out?->format('H:i') ?? '-' }}</td>
                        <td class="text-right">{{ $a->work_hours ? number_format($a->work_hours, 1) : '-' }}</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td colspan="3" class="text-right">Total</td>
                    <td class="text-right">{{ number_format($rows->sum('work_hours'), 1) }} jam / {{ $rows->count() }} hari</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <div class="footer">Dicetak dari Bayaran.app — {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
