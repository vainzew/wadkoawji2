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
        $kategori = Kategori::count();
        $produk = Produk::count();
        $supplier = Supplier::count();
        $member = Member::count();

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        // === DEFAULT DATE RANGE FOR SALES METRICS ===
        // Default to today only
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
        
        // Get sales metrics for default date range
        $salesMetrics = $this->getSalesMetrics($date_start, $date_end);
        
        // Get top selling products and categories for default date range
        $topProducts = $this->getTopProducts($date_start, $date_end);
        $topCategories = $this->getTopCategories($date_start, $date_end);
        
        // Get stock alert data
        $lowStockProducts = $this->getLowStockProducts();
        $stockStats = $this->getStockStats();

        // === DATA GRAFIK HARIAN ===
        $data_tanggal = array();
        $data_income = array();
        $data_outcome = array();

        $temp_tanggal_awal = $tanggal_awal;
        while (strtotime($temp_tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($temp_tanggal_awal, 8, 2);

            // Income dari penjualan
            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$temp_tanggal_awal%")->sum('bayar');
            
            // Outcome dari pembelian + pengeluaran
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$temp_tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$temp_tanggal_awal%")->sum('nominal');
            $total_outcome = $total_pembelian + $total_pengeluaran;

            $data_income[] = $total_penjualan;
            $data_outcome[] = $total_outcome;

            $temp_tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($temp_tanggal_awal)));
        }

        // === DATA GRAFIK BULANAN ===
        $current_year = date('Y');
        $data_bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data_income_bulanan = array();
        $data_outcome_bulanan = array();

        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            
            // Income bulanan
            $income_bulanan = Penjualan::whereYear('created_at', $current_year)
                                      ->whereMonth('created_at', $i)
                                      ->sum('bayar');
            
            // Outcome bulanan
            $pembelian_bulanan = Pembelian::whereYear('created_at', $current_year)
                                         ->whereMonth('created_at', $i)
                                         ->sum('bayar');
            $pengeluaran_bulanan = Pengeluaran::whereYear('created_at', $current_year)
                                             ->whereMonth('created_at', $i)
                                             ->sum('nominal');
            $outcome_bulanan = $pembelian_bulanan + $pengeluaran_bulanan;

            $data_income_bulanan[] = $income_bulanan;
            $data_outcome_bulanan[] = $outcome_bulanan;
        }

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
        // 1. Gross Sales - Total penjualan kotor (total_harga) dalam range tanggal
        $grossSales = Penjualan::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ])->sum('total_harga');
        
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
                    (penjualan_detail.harga_jual * penjualan_detail.jumlah) - 
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
                DB::raw('SUM(penjualan_detail.harga_jual * penjualan_detail.jumlah) as total_omzet')
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
                DB::raw('SUM(penjualan_detail.harga_jual * penjualan_detail.jumlah) as total_omzet')
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