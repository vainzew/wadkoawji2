<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\TempPenjualan;
use App\Models\TempPenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use App\Services\PromoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanDetailController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();
        $member = Member::orderBy('nama')->get();
        $diskon = Setting::first()->diskon ?? 0;

        if ($id_penjualan = session('id_penjualan')) {
            $penjualan = TempPenjualan::find($id_penjualan);
            $memberSelected = $penjualan->member ?? new Member();

            return view('penjualan_detail.index', compact('produk', 'member', 'diskon', 'id_penjualan', 'penjualan', 'memberSelected'));
        } else {
            if (auth()->user()->level == 1) {
                return redirect()->route('transaksi.baru');
            } else {
                return redirect()->route('home');
            }
        }
    }

    public function data($id)
    {
        $detail = TempPenjualanDetail::with(['produk', 'promo'])
            ->where('id_penjualan', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['kode_produk'] = '<span class="label label-success">'. ($item->produk['kode_produk'] ?? 'N/A') .'</span>';
            $row['nama_produk'] = $item->nama_produk ?? $item->produk['nama_produk'] ?? 'Produk Tidak Ditemukan';
            // Tampilkan harga asli untuk item gratis (fallback ke harga produk jika harga_jual tersimpan 0 di data lama)
            $displayPrice = ($item->is_free_item && (int)($item->harga_jual) === 0)
                ? ($item->produk['harga_jual'] ?? 0)
                : $item->harga_jual;
            $row['harga_jual']  = 'Rp. '. format_uang($displayPrice);
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id_penjualan_detail .'" value="'. $item->jumlah .'">';
            
            $promoInfo = '';
            if ($item->promo_description) {
                $promoClass = $item->is_free_item ? 'label-success' : 'label-info';
                $promoText = $item->is_free_item ? 'GRATIS' : 'PROMO';
                $promoInfo = '<small><span class="label ' . $promoClass . '">' . $promoText . '</span> ' . $item->promo_description . '</small>';
            } else if ($item->diskon > 0) {
                $promoInfo = '<small><span class="label label-info">DISKON</span> ' . number_format($item->diskon, 1) . '%</small>';
            }
            $row['promo'] = $promoInfo;
            
            // Subtotal selalu berdasarkan kolom subtotal dari DB (item gratis = 0)
            $row['subtotal']    = 'Rp. '. format_uang($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('transaksi.destroy', $item->id_penjualan_detail) .'`)" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Item"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->subtotal;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'kode_produk' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'nama_produk' => '',
            'harga_jual'  => '',
            'jumlah'      => '',
            'promo'       => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_produk', 'jumlah', 'promo'])
            ->make(true);
    }

    /**
     * Method baru untuk menangani pencarian produk via barcode.
     * Dibuat sangat spesifik dan fokus.
     */
    public function cariProduk(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);
    
        // Cari produk HANYA di kolom 'barcode'. Tidak ada fallback.
        $produk = Produk::where('barcode', $request->barcode)->first();
    
        // Kembalikan hasilnya, entah itu objek produk atau null jika tidak ditemukan.
        return response()->json($produk);
    }

    /**
     * Method store yang sudah disederhanakan dan dibuat lebih aman.
     * Sekarang hanya menerima id_produk yang sudah divalidasi oleh frontend.
     */
// Ganti HANYA method store() di PenjualanDetailController.php dengan yang ini

    public function store(Request $request)
    {
        // BAGIAN 1: Pencarian Produk (Versi Perbaikan)
        // ===============================================
        $produk = Produk::find($request->id_produk);
        
        if (! $produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk dengan ID yang diberikan tidak valid.'
            ], 404);
        }

        try {
            \DB::beginTransaction();
            
            $detail = new TempPenjualanDetail();
            $detail->id_penjualan = $request->id_penjualan;
            $detail->id_produk = $produk->id_produk;
            $detail->nama_produk = $produk->nama_produk;
            $detail->harga_jual = $produk->harga_jual;
            $detail->jumlah = 1;
            // Awali dengan diskon produk (persen) jika ada
            $detail->diskon = (float)($produk->diskon ?? 0);
            $basePrice = $detail->harga_jual * $detail->jumlah;
            $detail->subtotal = $basePrice - (($detail->diskon / 100) * $basePrice);
            $detail->save();

            // BAGIAN 2: Logika Promo (Versi Asli Milikmu yang Dikembalikan)
            // ==============================================================
            \Log::info('=== PROMO DEBUG START ===', [
                'produk_id' => $produk->id_produk,
                'produk_name' => $produk->nama_produk,
                'harga_jual' => $produk->harga_jual,
                'penjualan_id' => $request->id_penjualan
            ]);

            $promoMessages = [];
            $promoSuggestions = [];
            
            try {
                if (class_exists('\App\Services\PromoService')) {
                    \Log::info('PromoService class exists, checking for promos...');
                    $promoService = new \App\Services\PromoService();
                    $promoResults = $promoService->checkAndApplyPromo(
                        $produk->id_produk, 
                        1,
                        $request->id_penjualan
                    );
                    
                    \Log::info('Promo results:', [
                        'results' => $promoResults,
                        'is_array' => is_array($promoResults),
                        'count' => is_array($promoResults) ? count($promoResults) : 0
                    ]);
                    
                    if ($promoResults && is_array($promoResults)) {
                        foreach ($promoResults as $promoResult) {
                            if (!is_array($promoResult) || !isset($promoResult['type'])) {
                                continue;
                            }
                            
                            try {
                                switch ($promoResult['type']) {
                                    case 'discount':
                                        if (isset($promoResult['promo'], $promoResult['description'], $promoResult['discount_amount'])) {
                                            $basePrice = $detail->harga_jual * $detail->jumlah;
                                            $promoDiscountPercent = $basePrice > 0 ? (($promoResult['discount_amount'] / $basePrice) * 100) : 0;
                                            $totalPercent = min(($detail->diskon ?? 0) + $promoDiscountPercent, 100);
                                            $newSubtotal = $basePrice - (($totalPercent / 100) * $basePrice);
                                            $detail->update([
                                                'id_promo' => $promoResult['promo']->id_promo,
                                                'promo_description' => $promoResult['description'],
                                                'diskon' => $totalPercent,
                                                'subtotal' => $newSubtotal
                                            ]);
                                            
                                            $promoMessages[] = $promoResult['description'];
                                        }
                                        break;
                                        
                                    case 'free_item':
                                    case 'free_different_item':
                                        if (isset($promoResult['free_product'], $promoResult['free_quantity'], 
                                                $promoResult['promo'], $promoResult['description'])) {
                                            
                                            $freeItem = $promoService->addFreeItemToTransaction(
                                                $request->id_penjualan,
                                                $promoResult['free_product'],
                                                $promoResult['free_quantity'],
                                                $promoResult['promo'],
                                                $promoResult['description']
                                            );
                                            
                                            if ($freeItem) {
                                                $promoMessages[] = $promoResult['description'] . ' (Item gratis: ' . $freeItem->nama_produk . ' x' . $freeItem->jumlah . ')';
                                            }
                                        }
                                        break;
                                        
                                    case 'promo_suggestion':
                                        if (isset($promoResult['suggestion_message'])) {
                                            $promoSuggestions[] = $promoResult['suggestion_message'];
                                        }
                                        break;
                                }
                            } catch (\Exception $e) {
                                \Log::warning('Individual promo application failed: ' . $e->getMessage());
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Promo service failed, continuing without promo: ' . $e->getMessage());
            }

            \Log::info('=== PROMO DEBUG END ===', [
                'promo_messages_count' => count($promoMessages),
                'promo_suggestions_count' => count($promoSuggestions)
            ]);
            // ==============================================================
            // AKHIR DARI BLOK KODE PROMO
            // ==============================================================

            \DB::commit();
            
            // Reduced sleep time from 100000 microseconds (0.1s) to 50000 microseconds (0.05s)
            usleep(50000);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'data' => [
                    'promo_applied' => !empty($promoMessages),
                    'promo_messages' => $promoMessages,
                    'promo_suggestions' => $promoSuggestions,
                    'has_free_items' => count($promoMessages) > 0,
                    'has_suggestions' => count($promoSuggestions) > 0,
                    'detail_id' => $detail->id_penjualan_detail
                ]
            ], 200);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Store transaction failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $detail = TempPenjualanDetail::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Detail tidak ditemukan'], 404);
        }

        $oldQuantity = $detail->jumlah;
        $newQuantity = $request->jumlah;
        
        $detail->jumlah = $newQuantity;
        
        if ($detail->is_free_item) {
            // Item gratis: subtotal harus selalu 0 meskipun qty berubah
            $detail->subtotal = 0;
        } else {
            $basePrice = $detail->harga_jual * $newQuantity;
            $discountAmount = ($detail->diskon / 100) * $basePrice;
            $detail->subtotal = $basePrice - $discountAmount;
        }
        
        $detail->update();
        
        // ... (Logika update promo Anda jika ada) ...
        
        return response()->json('Data berhasil disimpan', 200);
    }

    public function destroy($id)
    {
        $detail = TempPenjualanDetail::find($id);
        if ($detail) {
            $detail->delete();
        }

        return response(null, 204);
    }

    public function loadForm($diskon = 0, $total = 0, $diterima = 0)
    {
        try {
            $diskon = floatval($diskon);
            $total = floatval($total);
            $diterima = floatval($diterima);
            
            // Pajak: gunakan setting->diskon sebagai pajak (%) + tax_enabled toggle
            $setting = \App\Models\Setting::first();
            $taxPercent = $setting->diskon ?? 0; // interpreted as Pajak (%)
            $taxEnabled = (bool)($setting->tax_enabled ?? false);

            $discountAmount = ($diskon / 100 * $total);
            $dpp = $total - $discountAmount; // dasar pengenaan pajak
            $taxAmount = $taxEnabled ? ($taxPercent / 100 * $dpp) : 0;

            $bayar   = $dpp + $taxAmount;
            $kembali = ($diterima != 0) ? $diterima - $bayar : 0;
            
            $data    = [
                'totalrp' => format_uang($total),
                'bayar' => $bayar,
                'bayarrp' => format_uang($bayar),
                'pajak' => $taxAmount,
                'pajakrp' => format_uang($taxAmount),
                'pajak_percent' => $taxEnabled ? $taxPercent : 0,
                'terbilang' => ucwords(terbilang($bayar). ' Rupiah'),
                'kembalirp' => format_uang($kembali),
                'kembali_terbilang' => ucwords(terbilang($kembali). ' Rupiah'),
            ];
            
            return response()->json(['status' => 'success', 'data' => $data]);
            
        } catch (\Exception $e) {
            Log::error('LoadForm error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Calculation failed'], 500);
        }
    }
}
