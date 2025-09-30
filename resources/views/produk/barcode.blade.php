<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode</title>

    <style>
        @page {
            margin: 10mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        
        .text-center {
            text-align: center;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        
        td {
            padding: 8px;
            border: 1px solid #ddd;
            width: 33.33%;
            vertical-align: middle;
            height: 120px;
        }
        
        .produk-info {
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 11px;
            line-height: 1.2;
        }
        
        .harga {
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .barcode-container {
            margin: 5px 0;
        }
        
        .barcode-container img {
            display: block;
            margin: 0 auto;
            max-width: 100%;
        }
        
        .kode-produk {
            font-size: 10px;
            font-weight: bold;
            margin-top: 3px;
            color: #333;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <h2 class="text-center" style="margin-bottom: 20px;">BARCODE PRODUK</h2>
    
    @if(isset($dataproduk) && count($dataproduk) > 0)
        <table>
            @php
                // Convert array to collection if it's not already, then chunk it
                $produkCollection = is_array($dataproduk) ? collect($dataproduk) : $dataproduk;
                $chunks = $produkCollection->chunk(3);
            @endphp
            @foreach ($chunks as $chunkIndex => $produkChunk)
                <tr>
                    @foreach ($produkChunk as $produk)
                        <td class="text-center">
                            <!-- Nama Produk dan Harga -->
                            <div class="produk-info">
                                {{ Str::limit($produk->nama_produk, 25) }}
                            </div>
                            <div class="harga">
                                Rp. {{ format_uang($produk->harga_jual) }}
                            </div>
                            
                            <!-- Barcode -->
                            <div class="barcode-container">
                                @php
                                    try {
                                        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                                        $barcode = $generator->getBarcode($produk->kode_produk, $generator::TYPE_CODE_128);
                                        $barcodeBase64 = base64_encode($barcode);
                                    } catch (\Exception $e) {
                                        $barcodeBase64 = null;
                                    }
                                @endphp
                                
                                @if($barcodeBase64)
                                    <img src="data:image/png;base64,{{ $barcodeBase64 }}" 
                                        alt="{{ $produk->kode_produk }}"
                                        style="width: 140px; height: 40px;">
                                @else
                                    <div style="border: 1px solid #ddd; width: 140px; height: 40px; margin: 0 auto; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                        <span style="font-size: 10px; color: #666;">Error Generate Barcode</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Kode Produk -->
                            <div class="kode-produk">
                                {{ $produk->kode_produk }}
                            </div>
                        </td>
                    @endforeach
                    
                    <!-- Fill empty cells if less than 3 products in last row -->
                    @for ($i = count($produkChunk); $i < 3; $i++)
                        <td></td>
                    @endfor
                </tr>
                
                <!-- Add page break every 12 products (4 rows) -->
                @if (($chunkIndex + 1) % 4 == 0 && !$loop->last)
                    </table>
                    <div class="page-break"></div>
                    <table>
                @endif
            @endforeach
        </table>
        
        <!-- Footer info -->
        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
            <p>Dicetak pada: {{ date('d/m/Y H:i:s') }} | Total: {{ count($dataproduk) }} produk</p>
        </div>
    @else
        <div class="text-center" style="margin-top: 50px;">
            <p>Tidak ada produk yang dipilih untuk dicetak.</p>
        </div>
    @endif
</body>
</html>