<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function scanBarcode(Request $request)
    {
        $barcodeValue = $request->input('barcode');
        // Logika untuk mencari produk berdasarkan barcode
        $product = Product::where('barcode', $barcodeValue)->first();
        return response()->json($product);
    }
}
