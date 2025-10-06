<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\Produk;
use PDF;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProdukImport;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produk = Produk::count();
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');

        return view('produk.index', compact('kategori', 'produk'));
    }

    public function data(Request $request)
    {
        // Make sure we're selecting the right columns that exist in the database
        $query = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select([
                'produk.id_produk',
                'produk.id_kategori',
                'produk.kode_produk',
                'produk.nama_produk',
                'produk.merk',
                'produk.harga_beli',
                'produk.diskon',
                'produk.harga_jual',
                'produk.stok',
                'produk.expired_at',
                'produk.created_at',
                'produk.updated_at',
                'produk.barcode',
                'kategori.nama_kategori'
            ]);

        // Optional filter: show only low stock (stok <= 1) and order by stok ascending
        if ($request->boolean('low_stock')) {
            $query->where('produk.stok', '<=', 1)
                  ->orderBy('produk.stok', 'asc');
        }
            
        // Apply search if provided (for promo modal search functionality)
        if ($request->has('search_value') && !empty($request->search_value)) {
            $search = $request->search_value;
            $query->where(function($q) use ($search) {
                $q->where('produk.nama_produk', 'like', "%{$search}%")
                  ->orWhere('produk.kode_produk', 'like', "%{$search}%")
                  ->orWhere('kategori.nama_kategori', 'like', "%{$search}%");
            });
        }
            
        // Return DataTables response - this automatically handles pagination, search, and sorting
        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('select_all', function ($produk) {
                return '
                    <input type="checkbox" name="id_produk[]" value="'. $produk->id_produk .'">
                ';
            })
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-success">'. $produk->kode_produk .'</span>';
            })
            ->addColumn('harga_beli', function ($produk) {
                return format_uang($produk->harga_beli);
            })
            ->addColumn('harga_jual', function ($produk) {
                return format_uang($produk->harga_jual);
            })
            ->addColumn('kategori', function ($produk) {
                return $produk->nama_kategori ? $produk->nama_kategori : '-';
            })
            ->addColumn('stok', function ($produk) {
                return format_uang($produk->stok);
            })
            ->addColumn('expired_at', function ($produk) {
                return $produk->expired_at ? $produk->expired_at : '-';
            })
            ->addColumn('aksi', function ($produk) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('produk.update', $produk->id_produk) .'`)" class="btn-edit btn-icon" data-bs-toggle="tooltip" data-bs-placement="left" title="Edit"><i class="mynaui-edit"></i></button>
                    <button type="button" onclick="deleteData(`'. route('produk.destroy', $produk->id_produk) .'`)" class="btn-delete btn-icon" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete"><i class="mynaui-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk', 'select_all'])
            // Filter out any search on columns that might cause issues
            ->filterColumn('kategori', function($query, $keyword) {
                $query->where('kategori.nama_kategori', 'like', "%{$keyword}%");
            })
            ->filterColumn('stok', function($query, $keyword) {
                $query->where('produk.stok', 'like', "%{$keyword}%");
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'id_kategori' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|numeric',
            'barcode' => 'nullable|unique:produk,barcode',
            'expired_at' => 'nullable|regex:/\d{2}\/\d{2}/', // Validasi format MM/YY
            'diskon' => 'nullable|numeric|min:0|max:100',
        ]);

        // Generate kode_produk jika belum ada
        $produk = Produk::latest()->first() ?? new Produk();
        $request['kode_produk'] = 'P'. tambah_nol_didepan((int)$produk->id_produk +1, 6);
        
        $data = $request->all();
        $data['diskon'] = isset($data['diskon']) && $data['diskon'] !== '' ? (float)$data['diskon'] : 0;

        // Simpan data ke database
        $produk = Produk::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $produk
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required',
            'id_kategori' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|numeric',
            'barcode' => 'nullable|unique:produk,barcode,' . $id . ',id_produk',
            'expired_at' => 'nullable|regex:/\d{2}\/\d{2}/', // Validasi format MM/YY
            'diskon' => 'nullable|numeric|min:0|max:100',
        ]);

        $data = $request->all();
        $data['diskon'] = isset($data['diskon']) && $data['diskon'] !== '' ? (float)$data['diskon'] : 0;

        $produk = Produk::find($id);
        $produk->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $produk
        ], 200);
    }

    public function show($id)
    {
        $produk = Produk::find($id);
        
        // TIDAK PERLU KONVERSI - data sudah dalam format MM/YY

        return response()->json([
            'status' => 'success',
            'data' => $produk
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $produk = Produk::findOrFail($id);
            $produk->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete selected products
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteSelected(Request $request)
    {
        try {
            $ids = $request->id_produk;
            
            if (empty($ids)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data yang dipilih'
                ], 400);
            }

            Produk::whereIn('id_produk', $ids)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data terpilih berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cetak barcode untuk produk terpilih
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cetakBarcode(Request $request)
    {
        $dataproduk = array();
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $dataproduk[] = $produk;
        }

        $pdf = PDF::loadView('produk.barcode', compact('dataproduk'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('produk.pdf');
    }

    /**
     * Cari produk untuk transaksi penjualan
     * Digunakan di halaman kasir untuk mencari produk berdasarkan barcode/nama
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cariProduk(Request $request)
    {
        $produk = Produk::where('barcode', $request->barcode)->orWhere('nama_produk', 'like', "%{$request->barcode}%")->first();

        if (!$produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $produk
        ], 200);
    }

    /**
     * Periksa stok produk yang rendah
     * Digunakan untuk notifikasi stok rendah di halaman kasir
     *
     * @return \Illuminate\Http\Response
     */
    public function checkLowStock()
    {
        $lowStockProducts = Produk::whereColumn('stok', '<=', 'stok_minimal')->get();

        return response()->json([
            'status' => 'success',
            'data' => $lowStockProducts
        ], 200);
    }

    /**
     * Import produk dari Excel/CSV berbasis nama_kategori (auto-create kategori).
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
            'mode' => 'nullable|in:insert,upsert',
        ]);

        $mode = $request->input('mode', 'upsert');

        try {
            $import = new ProdukImport($mode);
            Excel::import($import, $request->file('file'));

            $failures = method_exists($import, 'failures') ? $import->failures() : [];
            return response()->json([
                'status' => 'success',
                'message' => 'Import selesai',
                'summary' => [
                    'inserted' => $import->inserted,
                    'updated'  => $import->updated,
                    'failed'   => is_countable($failures) ? count($failures) : 0,
                ],
                'failures' => $failures,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Import gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unduh template CSV untuk import produk (opsi kategori berdasarkan nama).
     */
    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_produk.csv"',
        ];

        $callback = function() {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['nama_produk','nama_kategori','harga_beli','harga_jual','stok','barcode','kode_produk','merk','diskon','expired_at']);
            fputcsv($out, ['Puyer 16 [renteng]','Obat Bebas','9500','10000','20','0012345678901','','Konimex','0','12/26']);
            fputcsv($out, ['Sabun Mandi Z','Sabun','4500','6000','50','','','Lifebuoy','0','']);
            fclose($out);
        };

        return Response::stream($callback, 200, $headers);
    }
}
