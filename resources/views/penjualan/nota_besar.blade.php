<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota PDF</title>

    <style>
        table td {
            /* font-family: Arial, Helvetica, sans-serif; */
            font-size: 14px;
        }
        table.data td,
        table.data th {
            border: 1px solid #ccc;
            padding: 5px;
        }
        table.data {
            border-collapse: collapse;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <td rowspan="4" width="60%">
                <img src="{{ public_path($setting->path_logo) }}" alt="{{ $setting->path_logo }}" width="120">
                <br>
                {{ $setting->alamat }}
                <br>
                <br>
            </td>
            <td>Tanggal</td>
            <td>: {{ tanggal_indonesia(date('Y-m-d')) }}</td>
        </tr>
        <tr>
            <td>Kode Member</td>
            <td>: {{ $penjualan->member->kode_member ?? '' }}</td>
        </tr>
    </table>

    <table class="data" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $key => $item)
                <tr>
                    <td class="text-center">{{ $key+1 }}</td>
                    <td>{{ $item->produk->nama_produk }}</td>
                    <td>{{ $item->produk->kode_produk }}</td>
                    <td class="text-right">{{ format_uang($item->harga_jual) }}</td>
                    <td class="text-right">{{ format_uang($item->jumlah) }}</td>
                    <td class="text-right">{{ format_uang($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><b>Total Harga</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->total_harga) }}</b></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><b>Diskon</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diskon) }}</b></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><b>Total Bayar</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->bayar) }}</b></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><b>Metode Pembayaran</b></td>
                <td class="text-right"><b>{{ $penjualan->metode_pembayaran }}</b></td>
            </tr>
            @if($penjualan->metode_pembayaran === 'CASH')
            <tr>
                <td colspan="5" class="text-right"><b>Diterima</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diterima) }}</b></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><b>Kembali</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diterima - $penjualan->bayar) }}</b></td>
            </tr>
            @else
            <tr>
                <td colspan="5" class="text-right"><b>Status Pembayaran</b></td>
                <td class="text-right"><b>{{ $penjualan->status_pembayaran }}</b></td>
            </tr>
            @endif
        </tfoot>
    </table>

    @if($penjualan->metode_pembayaran === 'QRIS' && $penjualan->qr_code_url)
    <br>
    <table width="100%" style="margin-top: 20px;">
        <tr>
            <td class="text-center" colspan="2">
                <h3>SCAN QR CODE UNTUK PEMBAYARAN</h3>
                <br>
                <div style="border: 2px solid #000; padding: 10px; display: inline-block;">
                    <img src="{{ $penjualan->qr_code_url }}" alt="QR Code" width="150" height="150">
                </div>
                <br><br>
                <b>Order ID: {{ $penjualan->midtrans_order_id }}</b><br>
                @if($penjualan->payment_expired_at)
                <b>Berlaku sampai: {{ date('d/m/Y H:i', strtotime($penjualan->payment_expired_at)) }}</b>
                @endif
            </td>
        </tr>
    </table>
    @endif

    <table width="100%">
        <tr>
            <td><b>Terimakasih telah berbelanja dan sampai jumpa</b></td>
            <td class="text-center">
                Kasir
                <br>
                <br>
                {{ auth()->user()->name }}
            </td>
        </tr>
    </table>
</body>
</html>