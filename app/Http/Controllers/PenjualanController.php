<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\TempPenjualan;
use App\Models\TempPenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use App\Services\TelegramService;
use App\Services\MidtransService; // Tambahkan import Midtrans
use Illuminate\Http\Request;
use PDF;

class PenjualanController extends Controller
{
    protected $telegramService;
    protected $midtransService; // Tambahkan properti Midtrans

    // Update constructor dengan dependency injection
    public function __construct(TelegramService $telegramService, MidtransService $midtransService)
    {
        $this->telegramService = $telegramService;
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        // Remove the redirect and show the sales list page
        return view('penjualan.index');
    }

    // Rest of the controller methods remain the same
    public function data()
    {
        // Use pagination to limit the number of records loaded at once
        $penjualan = Penjualan::with('member')
            ->orderBy('id_penjualan', 'desc')
            ->limit(1000); // Limit to 1000 records for better performance

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->bayar);
            })
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            ->addColumn('kode_member', function ($penjualan) {
                $member = $penjualan->member->kode_member ?? '';
                return '<span class="label">'. $member .'</spa>';
            })
            ->addColumn('metode_pembayaran', function ($penjualan) {
                $badgeClass = $penjualan->metode_pembayaran === 'QRIS' ? 'label-info' : 'label-default';
                return '<span class="label label-success '. $badgeClass .'">'. $penjualan->metode_pembayaran .'</span>';
            })
            ->addColumn('status_pembayaran', function ($penjualan) {
                $colors = [
                    'LUNAS' => '#059669',      // Green
                    'PENDING' => '#ffc107',    // Yellow
                    'GAGAL' => '#dc3545',      // Red
                    'DIBATALKAN' => '#333'     // Black
                ];
                $color = $colors[$penjualan->status_pembayaran] ?? '#333';
                return '<span style="background: '. $color .'; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px;">'. $penjualan->status_pembayaran .'</span>';
            })
            ->editColumn('diskon', function ($penjualan) {
                return $penjualan->diskon . '%';
            })
            ->editColumn('kasir', function ($penjualan) {
                // Cache user data to avoid repeated queries
                if (!isset($penjualan->user_name)) {
                    $userCacheKey = 'user_name_' . $penjualan->id_user;
                    $penjualan->user_name = cache($userCacheKey, function() use ($penjualan) {
                        return $penjualan->user->name ?? $penjualan->id_user ?? '';
                    }, now()->addHours(1));
                }
                return $penjualan->user_name;
            })
            ->addColumn('aksi', function ($penjualan) {
                $buttons = '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('penjualan.show', $penjualan->id_penjualan) .'`)" class="btn-edit btn-icon" data-bs-toggle="tooltip" data-bs-placement="left" title="View"><i class="mynaui-eye"></i></button>';

                // Add check payment status button for QRIS pending payments
                if ($penjualan->metode_pembayaran === 'QRIS' && $penjualan->status_pembayaran === 'PENDING') {
                    $buttons .= '
                    <button onclick="checkPaymentStatus(`'. route('penjualan.check-payment', $penjualan->id_penjualan) .'`)" class="btn btn-warning btn-sm" title="Check Payment Status"><i class="fa fa-refresh"></i></button>';
                }
                
                $buttons .= '
                    <button onclick="deleteData(`'. route('penjualan.destroy', $penjualan->id_penjualan) .'`)" class="btn-delete btn-icon" data-bs-toggle="tooltip" data-bs-placement="right" title="Hapus"><i class="mynaui-trash"></i></button>
                </div>';
                
                return $buttons;
            })
            ->rawColumns(['aksi', 'kode_member', 'metode_pembayaran', 'status_pembayaran'])
            ->make(true);
    }

    public function create()
    {
        $penjualan = new TempPenjualan();
        $penjualan->id_member = null;
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->diskon = 0;
        $penjualan->bayar = 0;
        $penjualan->diterima = 0;
        $penjualan->id_user = auth()->id();
        $penjualan->save();

        session(['id_penjualan' => $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');
    }

    public function store(Request $request)
    {
        // Find the temp penjualan record
        $tempPenjualan = TempPenjualan::findOrFail($request->id_penjualan);
        
        // Create a new penjualan record with the same data
        $penjualan = new Penjualan();
        $penjualan->id_member = $request->id_member;
        $penjualan->total_item = $request->total_item;
        $penjualan->total_harga = $request->total;
        $penjualan->diskon = 0; // No manual discount, promo handles discounts
        $penjualan->bayar = $request->bayar;
        $penjualan->metode_pembayaran = $request->metode_pembayaran ?? 'CASH';
        
        // Handle payment method
        if ($penjualan->metode_pembayaran === 'QRIS') {
            \Log::info('=== QRIS PAYMENT DEBUG START ===', [
                'order_total' => $penjualan->bayar,
                'member_id' => $penjualan->id_member,
                'member_name' => $penjualan->member->nama ?? 'No member'
            ]);
            
            // QRIS Payment Flow
            $penjualan->diterima = $penjualan->bayar; // Set diterima sama dengan bayar untuk QRIS
            $penjualan->status_pembayaran = 'PENDING';
            
            // Generate order ID
            $orderId = $this->midtransService->generateOrderId('TRX');
            $penjualan->midtrans_order_id = $orderId;
            $penjualan->payment_expired_at = $this->midtransService->getPaymentExpiry(30); // 30 minutes
            
            \Log::info('Generated order ID:', ['order_id' => $orderId]);
            
            // Create QRIS payment
            try {
                $qrisResult = $this->midtransService->createQrisPayment(
                    $orderId,
                    $penjualan->bayar,
                    [
                        'first_name' => $penjualan->member->nama ?? 'Customer',
                        'email' => 'customer@example.com',
                        'phone' => '08123456789'
                    ]
                );
                
                \Log::info('QRIS creation result:', $qrisResult);
                
                if ($qrisResult['success']) {
                    // Get QR code URL from Midtrans response
                    $qrCodeUrl = $this->midtransService->getQrCodeUrl($qrisResult['data']);
                    $penjualan->qr_code_url = $qrCodeUrl;
                    
                    \Log::info('QRIS payment successful:', [
                        'qr_code_url' => $qrCodeUrl,
                        'order_id' => $orderId
                    ]);
                } else {
                    \Log::error('QRIS payment creation failed:', [
                        'error' => $qrisResult['error'] ?? 'Unknown error',
                        'result' => $qrisResult
                    ]);
                    
                    // Check for specific error codes
                    $errorMessage = $qrisResult['error'] ?? 'Unknown error';
                    $userFriendlyMessage = '';
                    
                    if (strpos($errorMessage, '402') !== false || strpos($errorMessage, 'Payment channel is not activated') !== false) {
                        $userFriendlyMessage = 'QRIS payment method belum diaktifkan di akun Midtrans. Silakan:\n1. Login ke Midtrans Dashboard Production\n2. Masuk ke Settings → Payment Methods\n3. Aktifkan QRIS payment\n4. Atau gunakan metode pembayaran Cash terlebih dahulu.';
                    } elseif (strpos($errorMessage, '401') !== false) {
                        $userFriendlyMessage = 'Kredensial Midtrans tidak valid. Periksa Server Key production.';
                    } elseif (strpos($errorMessage, '400') !== false) {
                        $userFriendlyMessage = 'Request tidak valid. Periksa format data transaksi.';
                    } else {
                        $userFriendlyMessage = 'QRIS payment gagal: ' . $errorMessage;
                    }
                    
                    // If QRIS creation fails, show error and stay on page
                    return redirect()->back()->withErrors([
                        'qris_error' => $userFriendlyMessage
                    ])->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('QRIS payment exception:', [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]);
                
                return redirect()->back()->withErrors([
                    'qris_error' => 'QRIS payment error: ' . $e->getMessage()
                ])->withInput();
            }
            
            \Log::info('=== QRIS PAYMENT DEBUG END ===');
        } else {
            // Cash Payment
            $penjualan->diterima = $request->diterima;
            $penjualan->status_pembayaran = 'LUNAS';
        }
        
        $penjualan->id_user = $tempPenjualan->id_user;
        $penjualan->save();

        // Move all temp penjualan detail records to penjualan detail
        $tempDetails = TempPenjualanDetail::where('id_penjualan', $tempPenjualan->id_penjualan)->get();
        foreach ($tempDetails as $tempDetail) {
            $detail = new PenjualanDetail();
            $detail->id_penjualan = $penjualan->id_penjualan;
            $detail->id_produk = $tempDetail->id_produk;
            $detail->nama_produk = $tempDetail->nama_produk;
            $detail->harga_jual = $tempDetail->harga_jual;
            $detail->jumlah = $tempDetail->jumlah;
            $detail->diskon = $tempDetail->diskon;
            $detail->id_promo = $tempDetail->id_promo;
            $detail->promo_description = $tempDetail->promo_description;
            $detail->is_free_item = $tempDetail->is_free_item;
            $detail->subtotal = $tempDetail->subtotal;
            $detail->save();
            
            // Update stock for each product
            $produk = Produk::find($tempDetail->id_produk);
            if ($produk) {
                $produk->stok -= $tempDetail->jumlah;
                $produk->update();
            }
        }

        // Delete temp records after successful transfer
        TempPenjualanDetail::where('id_penjualan', $tempPenjualan->id_penjualan)->delete();
        $tempPenjualan->delete();

        // Update session with new penjualan ID
        session(['id_penjualan' => $penjualan->id_penjualan]);

        // Send notification only for completed cash payments
        if ($penjualan->status_pembayaran === 'LUNAS') {
            $this->telegramService->sendTransactionNotification($penjualan);
        }

        return redirect()->route('transaksi.selesai');
    }

    /**
     * Check QRIS payment status
     */
    public function checkPaymentStatus($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        
        if ($penjualan->metode_pembayaran !== 'QRIS' || !$penjualan->midtrans_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method or order ID'
            ]);
        }
        
        $statusResult = $this->midtransService->checkPaymentStatus($penjualan->midtrans_order_id);
        
        if ($statusResult['success']) {
            $response = $statusResult['data'];
            $newStatus = $response->transaction_status;
            
            // Update payment status
            $updated = $this->midtransService->updatePaymentStatus(
                $penjualan->midtrans_order_id, 
                $newStatus, 
                $penjualan->id_penjualan
            );
            
            if ($updated) {
                // Refresh penjualan data
                $penjualan->refresh();
                
                // Send notification if payment is completed
                if ($penjualan->status_pembayaran === 'LUNAS') {
                    $this->telegramService->sendTransactionNotification($penjualan);
                }
                
                return response()->json([
                    'success' => true,
                    'status' => $penjualan->status_pembayaran,
                    'message' => 'Payment status updated: ' . $penjualan->status_pembayaran
                ]);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to check payment status'
        ]);
    }

    public function show($id)
    {
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                // Prioritas: gunakan nama_produk dari detail jika ada, jika tidak ada gunakan dari relasi produk
                return $detail->nama_produk ?? $detail->produk->nama_produk ?? 'Produk Tidak Ditemukan';
            })
            ->addColumn('harga_jual', function ($detail) {
                return 'Rp. '. format_uang($detail->harga_jual);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return 'Rp. '. format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::find($id);
        $detail    = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->update();
            }

            $item->delete();
        }

        $penjualan->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();

        return view('penjualan.selesai', compact('setting'));
    }

    public function notaKecil()
    {
        $setting = Setting::first();
        
        // Try to find in penjualan table first (for completed transactions)
        $penjualan = Penjualan::find(session('id_penjualan'));
        
        // If not found in penjualan table, try temp_penjualan table (for ongoing transactions)
        if (!$penjualan) {
            $penjualan = TempPenjualan::find(session('id_penjualan'));
        }
        
        if (!$penjualan) {
            abort(404);
        }
        
        // Try to find details in penjualan_detail table first
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();
            
        // If no details found, try temp_penjualan_detail table
        if ($detail->isEmpty()) {
            $detail = TempPenjualanDetail::with('produk')
                ->where('id_penjualan', session('id_penjualan'))
                ->get();
        }
        
        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }

    public function notaBesar()
    {
        $setting = Setting::first();
        
        // Try to find in penjualan table first (for completed transactions)
        $penjualan = Penjualan::find(session('id_penjualan'));
        
        // If not found in penjualan table, try temp_penjualan table (for ongoing transactions)
        if (!$penjualan) {
            $penjualan = TempPenjualan::find(session('id_penjualan'));
        }
        
        if (! $penjualan) {
            abort(404);
        }
        
        // Try to find details in penjualan_detail table first
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();
            
        // If no details found, try temp_penjualan_detail table
        if ($detail->isEmpty()) {
            $detail = TempPenjualanDetail::with('produk')
                ->where('id_penjualan', session('id_penjualan'))
                ->get();
        }

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('Transaksi-'. date('Y-m-d-his') .'.pdf');
    }
}