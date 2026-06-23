<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Hutang {{ $period }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 4px; }
        h2 { font-size: 11px; text-align: center; color: #666; margin-top: 0; margin-bottom: 20px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; border-bottom: 2px solid #d1d5db; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 8px; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-unpaid { background: #fee2e2; color: #991b1b; }
        .total-row td { font-weight: bold; background: #f9fafb; }
        .footer { text-align: center; color: #9ca3af; font-size: 8px; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Rekap Hutang Karyawan</h1>
    <h2>{{ $companyName }} — {{ $period }}</h2>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Karyawan</th>
                <th>Keterangan</th>
                <th class="text-right">Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($debts as $d)
                <tr>
                    <td>{{ $d->debt_date->format('d M Y') }}</td>
                    <td>{{ $d->employee->name }}</td>
                    <td>{{ $d->description ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $d->is_paid ? 'badge-paid' : 'badge-unpaid' }}">
                            {{ $d->is_paid ? 'Lunas' : 'Belum' }}
                        </span>
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">Rp {{ number_format($totalDebt, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">Dicetak dari Bayaran.app — {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
