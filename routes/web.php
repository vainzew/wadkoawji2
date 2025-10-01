<?php

use App\Http\Controllers\{
    DashboardController,
    KategoriController,
    LaporanController,
    ProdukController,
    MemberController,
    PengeluaranController,
    PembelianController,
    PembelianDetailController,
    PenjualanController,
    PenjualanDetailController,
    SettingController,
    SupplierController,
    UserController,
    KasController,
    PromoController,
    MidtransWebhookController,
    ActivationController,
    CatatanController,
};
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::match(['get', 'head'], '/', function () {
    // Check activation status without cache for security
    $status = checkActivationStatus();
    
    // If activation files are missing or invalid, force activation
    if ($status['status'] !== 'active') {
        return redirect()->route('activation.form');
    }
    
    // Only redirect to login if activation is valid
    return redirect()->route('login');
});

// Activation routes (tidak perlu auth)
Route::prefix('activation')->name('activation.')->group(function () {
    Route::get('/', [ActivationController::class, 'showActivationForm'])->name('form');
    Route::post('/activate', [ActivationController::class, 'activate'])->name('activate');
    Route::get('/status', [ActivationController::class, 'checkStatus'])->name('status');
    Route::post('/deactivate', [ActivationController::class, 'deactivate'])->name('deactivate');
});

// Login routes - PROTECTED by activation middleware
Route::middleware('activation')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Midtrans Webhook Routes (tidak perlu auth)
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handleNotification'])->name('midtrans.webhook');
Route::get('/payment/success', [MidtransWebhookController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/failed', [MidtransWebhookController::class, 'paymentFailed'])->name('payment.failed');

Route::group(['middleware' => ['auth', 'activation']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::resource('/kategori', KategoriController::class);

        Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');
        Route::post('/produk/delete-selected', [ProdukController::class, 'deleteSelected'])->name('produk.delete_selected');
        Route::post('/produk/cetak-barcode', [ProdukController::class, 'cetakBarcode'])->name('produk.cetak_barcode');
        Route::resource('/produk', ProdukController::class);
        
        Route::get('/promo/data', [PromoController::class, 'data'])->name('promo.data');
        Route::resource('/promo', PromoController::class);
        Route::get('/kas/data', [KasController::class, 'data'])->name('kas.data');
        Route::resource('/kas', KasController::class);
        Route::get('/dashboard/sales-metrics', [DashboardController::class, 'getSalesMetricsAjax'])->name('dashboard.sales-metrics');

        Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
        Route::post('/member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
        Route::resource('/member', MemberController::class);

        Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
        Route::resource('/supplier', SupplierController::class);

        Route::get('/pengeluaran/data', [PengeluaranController::class, 'data'])->name('pengeluaran.data');
        Route::resource('/pengeluaran', PengeluaranController::class);

        Route::get('/pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');
        Route::get('/pembelian/{id}/create', [PembelianController::class, 'create'])->name('pembelian.create');
        Route::resource('/pembelian', PembelianController::class)
            ->except('create');

        Route::get('/pembelian_detail/{id}/data', [PembelianDetailController::class, 'data'])->name('pembelian_detail.data');
        Route::get('/pembelian_detail/loadform/{diskon}/{total}', [PembelianDetailController::class, 'loadForm'])->name('pembelian_detail.load_form');
        Route::resource('/pembelian_detail', PembelianDetailController::class)
            ->except('create', 'show', 'edit');

        Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::post('/penjualan/{id}/check-payment', [PenjualanController::class, 'checkPaymentStatus'])->name('penjualan.check-payment');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    });

    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
        Route::post('/transaksi/cari-produk', [PenjualanDetailController::class, 'cariProduk'])->name('transaksi.cariProduk');
        Route::post('/transaksi/simpan', [PenjualanController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/selesai', [PenjualanController::class, 'selesai'])->name('transaksi.selesai');
        Route::get('/transaksi/nota-kecil', [PenjualanController::class, 'notaKecil'])->name('transaksi.nota_kecil');
        Route::get('/transaksi/nota-besar', [PenjualanController::class, 'notaBesar'])->name('transaksi.nota_besar');
        Route::get('/check-low-stock', [ProdukController::class, 'checkLowStock'])->name('produk.check_stock');
        Route::get('/transaksi/{id}/data', [PenjualanDetailController::class, 'data'])->name('transaksi.data');
        Route::get('/transaksi/loadform/{diskon}/{total}/{diterima}', [PenjualanDetailController::class, 'loadForm'])->name('transaksi.load_form');
        Route::resource('/transaksi', PenjualanDetailController::class)
            ->except('create', 'show', 'edit');

        // Catatan (Notes) - Admin & Kasir
        Route::get('/catatan', [CatatanController::class, 'index'])->name('catatan.index');
        Route::post('/catatan', [CatatanController::class, 'store'])->name('catatan.store');
        Route::put('/catatan/{catatan}', [CatatanController::class, 'update'])->name('catatan.update');
        Route::delete('/catatan/{catatan}', [CatatanController::class, 'destroy'])->name('catatan.destroy');
        Route::post('/catatan/update-order', [CatatanController::class, 'updateOrder'])->name('catatan.updateOrder');
    });

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/data', [LaporanController::class, 'getData'])->name('laporan.data');
        Route::get('/laporan/pdf', [LaporanController::class, 'exportPDF'])->name('laporan.export_pdf');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    });
 
    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');
        Route::post('/profil', [UserController::class, 'updateProfil'])->name('user.update_profil');
    });
});