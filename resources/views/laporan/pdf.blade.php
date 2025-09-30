<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pendapatan</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #f2f2f2;
            color: #333;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        tfoot th {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h3 class="text-center">Laporan Pendapatan</h3>
    <h4 class="text-center">
        Tanggal {{ tanggal_indonesia($tanggalAwal, false) }}
        s/d
        Tanggal {{ tanggal_indonesia($tanggalAkhir, false) }}
    </h4>

    <table class="table table-striped">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th class="text-right">Kas</th>
                <th class="text-right">Penjualan</th>
                <th class="text-right">Pembelian</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $row)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $row['tanggal'] }}</td>
                    <td class="text-right">{{ format_uang($row['kas']) }}</td>
                    <td class="text-right">{{ format_uang($row['penjualan']) }}</td>
                    <td class="text-right">{{ format_uang($row['pembelian']) }}</td>
                    <td class="text-right">{{ format_uang($row['pengeluaran']) }}</td>
                    <td class="text-right">{{ format_uang($row['pendapatan']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">Total</th>
                <th class="text-right">Rp {{ format_uang($totals['kas']) }}</th>
                <th class="text-right">Rp {{ format_uang($totals['penjualan']) }}</th>
                <th class="text-right">Rp {{ format_uang($totals['pembelian']) }}</th>
                <th class="text-right">Rp {{ format_uang($totals['pengeluaran']) }}</th>
                <th class="text-right">Rp {{ format_uang($totals['pendapatan']) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>