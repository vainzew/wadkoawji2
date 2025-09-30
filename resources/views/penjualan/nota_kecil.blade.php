<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Kecil</title>

    <style>
        @font-face {
            font-family: 'OCR A Extended';
            src: url('https://fonts.cdnfonts.com/css/ocr-a-extended');
        }

        * {
            margin: 0;
            padding: 0;
            font-family: 'OCR A Extended', 'OCRB', 'DotMatrix', 'Courier New', monospace;
            font-size: 11pt;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .text-center { 
            text-align: center; 
        }
        
        .text-right { 
            text-align: right; 
        }

        .container {
            width: 54mm;
            margin: 0 auto;
            padding: 0 2mm;
        }

        .header {
            margin-bottom: 2mm;
            text-align: center;
        }

        .header h3 {
            text-transform: uppercase;
            font-size: 13pt;
            font-weight: normal;
            margin-bottom: 1mm;
            letter-spacing: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .transaction-info {
            margin: 2mm 0;
        }

        .transaction-info td {
            padding: 0.5mm 0;
            font-size: 11pt;
        }

        .products td {
            padding: 0.5mm 0;
            font-size: 11pt;
        }

        .totals td {
            padding: 0.5mm 0;
            font-size: 11pt;
            font-weight: normal;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 2mm 0;
        }

        .footer {
            margin-top: 2mm;
            text-align: center;
        }

        .footer p {
            margin: 1mm 0;
            font-size: 10pt;
        }

        @media print {
            @page {
                margin: 0;
                size: 58mm auto;
            }
            
            html, body {
                width: 58mm;
                height: auto;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <h3>{{ $setting->nama_perusahaan }}</h3>
            <p>{{ $setting->alamat }}</p>
            <p>{{ $setting->telepon }}</p>
        </div>

        <div class="divider"></div>

        <div class="transaction-info">
            <table>
                <tr>
                    <td>{{ tambah_nol_didepan($penjualan->id_penjualan, 10) }} / {{ auth()->user()->name }}</td>
                </tr>
                <tr>
                    <td>{{ date('d.m.Y-H:i', strtotime($penjualan->created_at)) }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        <table class="products">
            @foreach ($detail as $item)
                <tr>
                    <td colspan="3">{{ $item->produk->nama_produk }}</td>
                </tr>
                <tr>
                    <td>{{ format_uang($item->harga_jual) }} x{{ $item->jumlah }}</td>
                    <td class="text-right">{{ format_uang($item->jumlah * $item->harga_jual) }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <table class="totals">
            <tr>
                <td>TOTAL</td>
                <td class="text-right">{{ format_uang($penjualan->total_harga) }}</td>
            </tr>
            @if($penjualan->metode_pembayaran === 'CASH')
            <tr>
                <td>BAYAR</td>
                <td class="text-right">{{ format_uang($penjualan->diterima) }}</td>
            </tr>
            <tr>
                <td>KEMBALI</td>
                <td class="text-right">{{ format_uang($penjualan->diterima - $penjualan->bayar) }}</td>
            </tr>
            @else
            <tr>
                <td>METODE</td>
                <td class="text-right">{{ $penjualan->metode_pembayaran }}</td>
            </tr>
            <tr>
                <td>STATUS</td>
                <td class="text-right">{{ $penjualan->status_pembayaran }}</td>
            </tr>
            @endif
        </table>
        
        @if($penjualan->metode_pembayaran === 'QRIS' && $penjualan->qr_code_url)
        <div class="divider"></div>
        
        <div class="qr-section" style="text-align: center; margin: 3mm 0;">
            <p style="font-size: 10pt; margin-bottom: 2mm;">SCAN QR CODE UNTUK BAYAR</p>
            <div style="display: inline-block; border: 1px solid #000; padding: 2mm;">
                <img src="{{ $penjualan->qr_code_url }}" 
                     alt="QR Code" 
                     style="width: 25mm; height: 25mm; display: block;">
            </div>
            <p style="font-size: 9pt; margin-top: 2mm;">Order ID: {{ $penjualan->midtrans_order_id }}</p>
            @if($penjualan->payment_expired_at)
            <p style="font-size: 9pt;">Berlaku sampai:</p>
            <p style="font-size: 9pt;">{{ date('d/m/Y H:i', strtotime($penjualan->payment_expired_at)) }}</p>
            @endif
        </div>
        @endif

        <div class="divider"></div>

        <div class="footer">
            <p>Terimakasih</p>
            <p>Belanja Hemat & Berkualitas</p>
            <p>Hanya di {{ $setting->nama_perusahaan }}</p>
        </div>
    </div>

    <script>
        window.onafterprint = function() {
            window.location.href = '{{ route('transaksi.selesai') }}';
        };
    </script>
</body>
</html>