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
                @php
                    $namaProduk = $item->nama_produk ?? ($item->produk->nama_produk ?? '-');
                    $base = (int)($item->harga_jual) * (int)($item->jumlah);
                    $sub = (int)($item->subtotal);
                    $disc = max($base - $sub, 0);
                    $isFree = (bool)($item->is_free_item ?? false);
                @endphp
                <tr>
                    <td colspan="3">{{ $namaProduk }}</td>
                </tr>
                <tr>
                    <td colspan="2">{{ format_uang($item->harga_jual) }} x{{ $item->jumlah }}</td>
                    <td class="text-right">{{ format_uang($sub) }}</td>
                </tr>
                @if(!$isFree && $disc > 0)
                <tr>
                    <td colspan="2">
                        @php
                            $diskonPersen = isset($item->diskon) ? (float)$item->diskon : null;
                            $labelDiskon = $diskonPersen ? ('Diskon ' . rtrim(rtrim(number_format($diskonPersen, 1, '.', ''), '0'), '.') . '%') : 'Diskon';
                        @endphp
                        <span>{{ $labelDiskon }}</span>
                    </td>
                    <td class="text-right">-{{ format_uang($disc) }}</td>
                </tr>
                @endif
            @endforeach
        </table>

        <div class="divider"></div>

        <table class="totals">
            @php
                $settingTaxEnabled = $setting->tax_enabled ?? false;
                $taxPercent = $setting->diskon ?? 0; // interpret as tax percent
                $taxAmount = $settingTaxEnabled ? ($taxPercent/100 * $penjualan->total_harga) : 0;
                $grandTotal = $penjualan->total_harga + $taxAmount;
            @endphp
            <tr>
                <td>SUBTOTAL</td>
                <td class="text-right">{{ format_uang($penjualan->total_harga) }}</td>
            </tr>
            @if($settingTaxEnabled && $taxPercent > 0)
            <tr>
                <td>PPN {{ rtrim(rtrim(number_format($taxPercent, 1, '.', ''), '0'), '.') }}%</td>
                <td class="text-right">{{ format_uang($taxAmount) }}</td>
            </tr>
            @endif
            <tr>
                <td>TOTAL</td>
                <td class="text-right">{{ format_uang($grandTotal) }}</td>
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

        <div class="qr-section" style="text-align: center; margin: 5mm 0;">
            <p style="font-size: 12pt; margin-bottom: 3mm; font-weight: medium;">
                SCAN QR CODE UNTUK BAYAR
            </p>
            
            <div style="display: inline-block; padding: 0;"> <!-- border dihapus -->
                <img src="{{ $penjualan->qr_code_url }}" 
                    alt="QR Code" 
                    style="width: 50mm; height: 50mm; display: block; margin: 0 auto;">
            </div>

            <p style="font-size: 10pt; margin-top: 3mm;">Order ID: {{ $penjualan->midtrans_order_id }}</p>
            
            @if($penjualan->payment_expired_at)
                <p style="font-size: 10pt; margin-top: 1mm;">Berlaku sampai:</p>
                <p style="font-size: 10pt;">
                    {{ date('d/m/Y H:i', strtotime($penjualan->payment_expired_at)) }}
                </p>
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
