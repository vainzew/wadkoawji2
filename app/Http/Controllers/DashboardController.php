<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Member;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // PERFORMANCE: Cache basic counts (5 minutes) - data jarang berubah drastis
        $cacheKey = 'dashboard_counts';
        $counts = cache()->remember($cacheKey, now()->addMinutes(5), function() {
            return [
                'kategori' => Kategori::count(),
                'produk' => Produk::count(),
                'supplier' => Supplier::count(),
                'member' => Member::count(),
            ];
        });
        
        $kategori = $counts['kategori'];
        $produk = $counts['produk'];
        $supplier = $counts['supplier'];
        $member = $counts['member'];

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        // === DEFAULT DATE RANGE FOR SALES METRICS ===
        // Default to today only
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
        
        // PERFORMANCE: Cache sales metrics (2 minutes) - updates lebih sering
        $metricsKey = 'dashboard_metrics_' . $date_start . '_' . $date_end;
        $salesMetrics = cache()->remember($metricsKey, now()->addMinutes(2), function() use ($date_start, $date_end) {
            return $this->getSalesMetrics($date_start, $date_end);
        });
        
        // PERFORMANCE: Cache top products and categories (3 minutes)
        $topProductsKey = 'dashboard_top_products_' . $date_start . '_' . $date_end;
        $topProducts = cache()->remember($topProductsKey, now()->addMinutes(3), function() use ($date_start, $date_end) {
            return $this->getTopProducts($date_start, $date_end);
        });
        
        $topCategoriesKey = 'dashboard_top_categories_' . $date_start . '_' . $date_end;
        $topCategories = cache()->remember($topCategoriesKey, now()->addMinutes(3), function() use ($date_start, $date_end) {
            return $this->getTopCategories($date_start, $date_end);
        });
        
        // PERFORMANCE: Cache stock data (5 minutes) - stok ga berubah tiap detik
        $lowStockProducts = cache()->remember('dashboard_low_stock', now()->addMinutes(5), function() {
            return $this->getLowStockProducts();
        });
        
        $stockStats = cache()->remember('dashboard_stock_stats', now()->addMinutes(5), function() {
            return $this->getStockStats();
        });

        // === DATA GRAFIK HARIAN ===
        // PERFORMANCE: Cache grafik harian (5 minutes) - update cepat untuk hari ini
        // Cache key include tanggal_akhir untuk real-time updates
        $grafikHarianKey = 'dashboard_grafik_harian_' . date('Y-m') . '_' . $tanggal_akhir;
        $grafikHarian = cache()->remember($grafikHarianKey, now()->addMinutes(5), function() use ($tanggal_awal, $tanggal_akhir) {
            $data_tanggal = array();
            $data_income = array();
            $data_outcome = array();
            
            // OPTIMIZATION: Single query untuk semua hari instead of loop
            $penjualanData = Penjualan::selectRaw('DATE(created_at) as tanggal, SUM(bayar) as total')
                ->whereBetween('created_at', [$tanggal_awal . ' 00:00:00', $tanggal_akhir . ' 23:59:59'])
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');
                
            $pembelianData = Pembelian::selectRaw('DATE(created_at) as tanggal, SUM(bayar) as total')
                ->whereBetween('created_at', [$tanggal_awal . ' 00:00:00', $tanggal_akhir . ' 23:59:59'])
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');
                
            $pengeluaranData = Pengeluaran::selectRaw('DATE(created_at) as tanggal, SUM(nominal) as total')
                ->whereBetween('created_at', [$tanggal_awal . ' 00:00:00', $tanggal_akhir . ' 23:59:59'])
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');

            $temp_tanggal_awal = $tanggal_awal;
            while (strtotime($temp_tanggal_awal) <= strtotime($tanggal_akhir)) {
                $data_tanggal[] = (int) substr($temp_tanggal_awal, 8, 2);
                
                $data_income[] = $penjualanData[$temp_tanggal_awal] ?? 0;
                $data_outcome[] = ($pembelianData[$temp_tanggal_awal] ?? 0) + ($pengeluaranData[$temp_tanggal_awal] ?? 0);

                $temp_tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($temp_tanggal_awal)));
            }
            
            return compact('data_tanggal', 'data_income', 'data_outcome');
        });
        
        $data_tanggal = $grafikHarian['data_tanggal'];
        $data_income = $grafikHarian['data_income'];
        $data_outcome = $grafikHarian['data_outcome'];

        // === DATA GRAFIK BULANAN ===
        // PERFORMANCE: Cache grafik bulanan (1 hour) - data bulanan lebih stabil
        $current_year = date('Y');
        $data_bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        $grafikBulananKey = 'dashboard_grafik_bulanan_' . $current_year;
        $grafikBulanan = cache()->remember($grafikBulananKey, now()->addHour(), function() use ($current_year) {
            // OPTIMIZATION: Single query untuk semua bulan instead of loop
            $penjualanBulanan = Penjualan::selectRaw('MONTH(created_at) as bulan, SUM(bayar) as total')
                ->whereYear('created_at', $current_year)
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
                
            $pembelianBulanan = Pembelian::selectRaw('MONTH(created_at) as bulan, SUM(bayar) as total')
                ->whereYear('created_at', $current_year)
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
                
            $pengeluaranBulanan = Pengeluaran::selectRaw('MONTH(created_at) as bulan, SUM(nominal) as total')
                ->whereYear('created_at', $current_year)
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
            
            $data_income_bulanan = array();
            $data_outcome_bulanan = array();
            
            for ($i = 1; $i <= 12; $i++) {
                $data_income_bulanan[] = $penjualanBulanan[$i] ?? 0;
                $data_outcome_bulanan[] = ($pembelianBulanan[$i] ?? 0) + ($pengeluaranBulanan[$i] ?? 0);
            }
            
            return compact('data_income_bulanan', 'data_outcome_bulanan');
        });
        
        $data_income_bulanan = $grafikBulanan['data_income_bulanan'];
        $data_outcome_bulanan = $grafikBulanan['data_outcome_bulanan'];

        if (auth()->user()->level == 1) {
            return view('admin.dashboard', compact(
                'kategori', 'produk', 'supplier', 'member', 
                'tanggal_awal', 'tanggal_akhir', 
                // Data harian
                'data_tanggal', 'data_income', 'data_outcome',
                // Data bulanan  
                'data_bulan', 'data_income_bulanan', 'data_outcome_bulanan',
                // Date range for sales metrics
                'date_start', 'date_end',
                // Top selling data
                'topProducts', 'topCategories',
                // Stock alert data
                'lowStockProducts', 'stockStats'
            ) + $salesMetrics);
        } else {
            return view('kasir.dashboard');
        }
    }

    /**
     * Get sales metrics for given date range
     */
    private function getSalesMetrics($startDate, $endDate)
    {
        // 1. Gross Sales - Total penjualan kotor sebelum promo/discount
        // Hitung dari detail transaksi dengan fallback untuk data lama (free item harga_jual=0)
        $grossSales = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->leftJoin('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->whereBetween('penjualan.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->selectRaw("SUM((CASE WHEN penjualan_detail.is_free_item = 1 AND penjualan_detail.harga_jual = 0 THEN COALESCE(produk.harga_jual, 0) ELSE penjualan_detail.harga_jual END) * penjualan_detail.jumlah) as gross_sum")
            ->value('gross_sum') ?? 0;
        
        // 2. Net Sales - Penjualan bersih (yang benar-benar dibayar) setelah diskon dalam range tanggal
        $netSales = Penjualan::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ])->sum('bayar');
        
        // 3. Total Transactions - Jumlah transaksi dalam range tanggal
        $totalTransactions = Penjualan::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ])->count();
        
        // 4. Calculate Gross Profit (Revenue - Cost of Goods Sold)
        $grossProfit = $this->calculateGrossProfit($startDate, $endDate);
        
        // 5. Calculate Gross Margin percentage
        $grossMargin = $grossSales > 0 ? ($grossProfit / $grossSales) * 100 : 0;
        
        // 6. Average Sale per Transaction - Rata-rata per transaksi
        $averageSale = $totalTransactions > 0 ? $netSales / $totalTransactions : 0;

        return [
            'grossSales' => $grossSales,
            'netSales' => $netSales,
            'totalTransactions' => $totalTransactions,
            'grossProfit' => $grossProfit,
            'grossMargin' => $grossMargin,
            'averageSale' => $averageSale
        ];
    }
    
    /**
     * Calculate Gross Profit (Revenue - COGS)
     */
    private function calculateGrossProfit($startDate, $endDate)
    {
        // Get all penjualan details in date range with their cost price (harga_beli)
        $grossProfit = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->whereBetween('penjualan.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->selectRaw('
                SUM(
                    (penjualan_detail.subtotal) - 
                    (produk.harga_beli * penjualan_detail.jumlah)
                ) as gross_profit
            ')
            ->value('gross_profit') ?? 0;
            
        return $grossProfit;
    }

    /**
     * Get products with low stock (≤ 10)
     */
    private function getLowStockProducts()
    {
        return Produk::where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->limit(15)
            ->get();
    }
    
    /**
     * Get stock statistics summary
     */
    private function getStockStats()
    {
        $outOfStock = Produk::where('stok', '<=', 0)->count();
        $lowStock = Produk::whereBetween('stok', [1, 10])->count();
        $goodStock = Produk::where('stok', '>', 10)->count();
        $totalProducts = Produk::count();
        
        return [
            'outOfStock' => $outOfStock,
            'lowStock' => $lowStock,
            'goodStock' => $goodStock,
            'totalProducts' => $totalProducts
        ];
    }

    /**
     * Get top 10 selling products for given date range
     */
    private function getTopProducts($startDate, $endDate)
    {
        return DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->whereBetween('penjualan.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->select(
                'produk.nama_produk',
                DB::raw('SUM(penjualan_detail.jumlah) as total_terjual'),
                DB::raw('SUM(penjualan_detail.subtotal) as total_omzet')
            )
            ->groupBy('penjualan_detail.id_produk', 'produk.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(10)
            ->get();
    }
    
    /**
     * Get top 10 selling categories for given date range
     */
    private function getTopCategories($startDate, $endDate)
    {
        return DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->join('kategori', 'produk.id_kategori', '=', 'kategori.id_kategori')
            ->whereBetween('penjualan.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->select(
                'kategori.nama_kategori',
                DB::raw('SUM(penjualan_detail.jumlah) as total_terjual'),
                DB::raw('SUM(penjualan_detail.subtotal) as total_omzet')
            )
            ->groupBy('kategori.id_kategori', 'kategori.nama_kategori')
            ->orderBy('total_terjual', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * AJAX endpoint untuk update sales metrics berdasarkan date range
     */
    public function getSalesMetricsAjax(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validate dates
        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Start date and end date are required'], 400);
        }

        $metrics = $this->getSalesMetrics($startDate, $endDate);
        $topProducts = $this->getTopProducts($startDate, $endDate);
        $topCategories = $this->getTopCategories($startDate, $endDate);

        return response()->json([
            'metrics' => $metrics,
            'topProducts' => $topProducts,
            'topCategories' => $topCategories
        ]);
    }
}
