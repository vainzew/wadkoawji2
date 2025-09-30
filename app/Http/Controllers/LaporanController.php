<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = $request->input('tanggal_awal', date('Y-m-01'));
        $tanggalAkhir = $request->input('tanggal_akhir', date('Y-m-d'));

        return view('laporan.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData(Request $request)
    {
        $awal = $request->input('tanggal_awal');
        $akhir = $request->input('tanggal_akhir');

        $data_tabel = $this->prepareReportData($awal, $akhir, true); // true for formatted data

        return datatables()->of($data_tabel)->make(true);
    }

    public function exportPDF(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal', date('Y-m-01'));
        $tanggalAkhir = $request->get('tanggal_akhir', date('Y-m-d'));

        $data = $this->prepareReportData($tanggalAwal, $tanggalAkhir, false); // false for raw data
        
        // Calculate totals for the footer
        $totals = [
            'kas' => array_sum(array_column($data, 'kas')),
            'penjualan' => array_sum(array_column($data, 'penjualan')),
            'pembelian' => array_sum(array_column($data, 'pembelian')),
            'pengeluaran' => array_sum(array_column($data, 'pengeluaran')),
            'pendapatan' => array_sum(array_column($data, 'pendapatan')),
        ];
        
        $pdf = PDF::loadView('laporan.pdf', compact('tanggalAwal', 'tanggalAkhir', 'data', 'totals'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Laporan-Pendapatan-'. date('Y-m-d-his') .'.pdf');
    }

    private function prepareReportData($awal, $akhir, $format_uang = false)
    {
        $penjualan = DB::table('penjualan as p')
            ->select(DB::raw('DATE(p.created_at) as tanggal'), DB::raw('SUM(p.bayar) as total_penjualan'))
            ->whereBetween('p.created_at', [$awal . ' 00:00:00', $akhir . ' 23:59:59'])
            ->where('p.status_pembayaran', 'LUNAS')
            ->groupBy('tanggal')->orderBy('tanggal', 'asc')->get()->keyBy('tanggal');

        $pembelian = DB::table('pembelian')
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(bayar) as total_pembelian'))
            ->whereBetween('created_at', [$awal . ' 00:00:00', $akhir . ' 23:59:59'])
            ->groupBy('tanggal')->get()->keyBy('tanggal');

        $pengeluaran = DB::table('pengeluaran')
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(nominal) as total_pengeluaran'))
            ->whereBetween('created_at', [$awal . ' 00:00:00', $akhir . ' 23:59:59'])
            ->groupBy('tanggal')->get()->keyBy('tanggal');

        $kas = DB::table('kas')
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(nominal_setoran) as total_kas'))
            ->whereBetween('created_at', [$awal . ' 00:00:00', $akhir . ' 23:59:59'])
            ->groupBy('tanggal')->get()->keyBy('tanggal');

        $data_laporan = [];
        $currentDate = new \DateTime($awal);
        $endDate = new \DateTime($akhir);
        $no = 1;

        while ($currentDate <= $endDate) {
            $tanggal = $currentDate->format('Y-m-d');
            $kas_harian = $kas[$tanggal]->total_kas ?? 0;
            $penjualan_harian = $penjualan[$tanggal]->total_penjualan ?? 0;
            $pembelian_harian = $pembelian[$tanggal]->total_pembelian ?? 0;
            $pengeluaran_harian = $pengeluaran[$tanggal]->total_pengeluaran ?? 0;
            $pendapatan_harian = $penjualan_harian - $pembelian_harian - $pengeluaran_harian;

            $rowData = [
                'DT_RowIndex' => $no++,
                'tanggal' => tanggal_indonesia($tanggal, false),
            ];

            if ($format_uang) {
                $rowData['kas'] = 'Rp ' . format_uang($kas_harian);
                $rowData['penjualan'] = 'Rp ' . format_uang($penjualan_harian);
                $rowData['pembelian'] = 'Rp ' . format_uang($pembelian_harian);
                $rowData['pengeluaran'] = 'Rp ' . format_uang($pengeluaran_harian);
                $rowData['pendapatan'] = 'Rp ' . format_uang($pendapatan_harian);
            } else {
                $rowData['kas'] = $kas_harian;
                $rowData['penjualan'] = $penjualan_harian;
                $rowData['pembelian'] = $pembelian_harian;
                $rowData['pengeluaran'] = $pengeluaran_harian;
                $rowData['pendapatan'] = $pendapatan_harian;
            }

            $data_laporan[] = $rowData;
            $currentDate->modify('+1 day');
        }
        return $data_laporan;
    }
}
