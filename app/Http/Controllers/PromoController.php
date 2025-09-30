<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Produk;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PromoController extends Controller
{
    public function index()
    {
        return view('promo.index');
    }

    public function data()
    {
        $promo = Promo::with(['produk', 'produkBuy', 'produkGet'])->orderBy('id_promo', 'desc');

        return DataTables::of($promo)
            ->addIndexColumn()
            ->addColumn('produk_terkait', function ($promo) {
                if ($promo->tipe_promo == 'buy_a_get_b_free') {
                    $buy = $promo->produkBuy ? $promo->produkBuy->nama_produk : '-';
                    $get = $promo->produkGet ? $promo->produkGet->nama_produk : '-';
                    return "Buy: {$buy}<br>Get: {$get}";
                } else {
                    $produk_names = $promo->produk->pluck('nama_produk')->take(3);
                    $display = $produk_names->implode(', ');
                    if ($promo->produk->count() > 3) {
                        $display .= ' (+' . ($promo->produk->count() - 3) . ' lainnya)';
                    }
                    return $display ?: '-';
                }
            })
            ->addColumn('detail_promo', function ($promo) {
                switch ($promo->tipe_promo) {
                    case 'percent_per_item':
                        return "Diskon {$promo->discount_percentage}%";
                    case 'b1g1_same_item':
                        return "Buy 1 Get 1 (Same Item)";
                    case 'buy_a_get_b_free':
                        return "Buy {$promo->buy_quantity} Get {$promo->get_quantity} Free";
                    default:
                        return '-';
                }
            })
            ->addColumn('status', function ($promo) {
                if ($promo->isValidPromo()) {
                    return '<span class="label label-success">Aktif</span>';
                } else {
                    return '<span class="label label-danger">Tidak Aktif</span>';
                }
            })
            ->addColumn('periode', function ($promo) {
                return $promo->start_date->format('d/m/Y') . ' - ' . $promo->end_date->format('d/m/Y');
            })
            ->addColumn('aksi', function ($promo) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('promo.update', $promo->id_promo) .'`)" class="btn-edit btn-icon" data-bs-toggle="tooltip" data-bs-placement="left" title="Edit"><i class="mynaui-edit"></i></button>
                    <button type="button" onclick="deleteData(`'. route('promo.destroy', $promo->id_promo) .'`)" class="btn-delete btn-icon" data-bs-toggle="tooltip" data-bs-placement="right" title="Hapus"><i class="mynaui-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'status', 'produk_terkait'])
            ->make(true);
    }

    public function create()
    {
        $produk = Produk::orderBy('nama_produk')->get();
        return view('promo.form', compact('produk'));
    }

    public function store(Request $request)
    {
        try {
            // Log incoming request for debugging
            \Log::info('Promo Store Request:', $request->all());
            
            $request->validate([
                'nama_promo' => 'required|string|max:255',
                'tipe_promo' => 'required|in:percent_per_item,b1g1_same_item,buy_a_get_b_free',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            // Clean data - remove produk_ids from main data as it's handled separately
            $data = $request->except(['produk_ids']);
            
            // Convert comma-separated produk_ids to array
            $produkIds = $this->processProdukIds($request);
            
            \Log::info('Processed produk IDs:', $produkIds);
            
            // Validasi berdasarkan tipe promo
            if ($request->tipe_promo == 'percent_per_item') {
                $request->validate([
                    'discount_percentage' => 'required|numeric|min:0|max:100',
                ]);
                
                if (empty($produkIds)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Minimal pilih 1 produk untuk promo percent per item'
                    ], 422);
                }
                
                // Set other fields to null for this type
                $data['buy_quantity'] = null;
                $data['get_quantity'] = null;
                $data['id_produk_buy'] = null;
                $data['id_produk_get'] = null;
                
            } elseif ($request->tipe_promo == 'buy_a_get_b_free') {
                $request->validate([
                    'buy_quantity' => 'required|integer|min:1',
                    'get_quantity' => 'required|integer|min:1',
                    'id_produk_buy' => 'required|exists:produk,id_produk',
                    'id_produk_get' => 'required|exists:produk,id_produk',
                ]);
                
                // Set discount_percentage to null for this type
                $data['discount_percentage'] = null;
                
            } elseif ($request->tipe_promo == 'b1g1_same_item') {
                \Log::info('B1G1 validation - produk IDs:', $produkIds);
                if (empty($produkIds)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Minimal pilih 1 produk untuk promo B1G1'
                    ], 422);
                }
                
                // Set other fields to null for this type
                $data['discount_percentage'] = null;
                $data['buy_quantity'] = null;
                $data['get_quantity'] = null;
                $data['id_produk_buy'] = null;
                $data['id_produk_get'] = null;
            }

            // Ensure is_active is set properly
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            
            \Log::info('Final promo data:', $data);
            
            // Create promo
            $promo = Promo::create($data);
            
            \Log::info('Promo created with ID:', ['id' => $promo->id_promo]);

            // Attach produk untuk tipe promo yang memerlukan
            if (in_array($request->tipe_promo, ['percent_per_item', 'b1g1_same_item']) && !empty($produkIds)) {
                $promo->produk()->sync($produkIds);
                \Log::info('Produk synced for promo:', ['promo_id' => $promo->id_promo, 'produk_ids' => $produkIds]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error in Promo Store:', $e->errors());
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error creating promo:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $promo = Promo::with(['produk', 'produkBuy', 'produkGet'])->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $promo
        ]);
    }

    public function edit($id)
    {
        $promo = Promo::with(['produk'])->findOrFail($id);
        $produk = Produk::orderBy('nama_produk')->get();
        
        return view('promo.form', compact('promo', 'produk'));
    }

    public function update(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);
        
        $request->validate([
            'nama_promo' => 'required|string|max:255',
            'tipe_promo' => 'required|in:percent_per_item,b1g1_same_item,buy_a_get_b_free',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $data = $request->all();
        
        // Convert comma-separated produk_ids to array
        $produkIds = $this->processProdukIds($request);
        
        // Validasi berdasarkan tipe promo
        if ($request->tipe_promo == 'percent_per_item') {
            $request->validate([
                'discount_percentage' => 'required|numeric|min:0|max:100',
            ]);
            
            if (empty($produkIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Minimal pilih 1 produk untuk promo percent per item'
                ], 422);
            }
        } elseif ($request->tipe_promo == 'buy_a_get_b_free') {
            $request->validate([
                'buy_quantity' => 'required|integer|min:1',
                'get_quantity' => 'required|integer|min:1',
                'id_produk_buy' => 'required|exists:produk,id_produk',
                'id_produk_get' => 'required|exists:produk,id_produk',
            ]);
        } elseif ($request->tipe_promo == 'b1g1_same_item') {
            if (empty($produkIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Minimal pilih 1 produk untuk promo B1G1'
                ], 422);
            }
        }

        $promo->update($data);

        // Sync produk untuk tipe promo yang memerlukan
        if (in_array($request->tipe_promo, ['percent_per_item', 'b1g1_same_item'])) {
            $promo->produk()->sync($produkIds ?? []);
        } else {
            $promo->produk()->detach();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diubah'
        ]);
    }

    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->produk()->detach(); // Hapus relasi
        $promo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * Process produk_ids from request (handle comma-separated string and array)
     */
    private function processProdukIds(Request $request)
    {
        $produkIds = [];
        
        if ($request->has('produk_ids')) {
            $produkData = $request->produk_ids;
            
            // Handle if it's an array (from form with produk_ids[])
            if (is_array($produkData)) {
                foreach ($produkData as $item) {
                    if (!empty($item)) {
                        // Split comma-separated values and add to array
                        $ids = explode(',', $item);
                        $produkIds = array_merge($produkIds, $ids);
                    }
                }
            } 
            // Handle if it's a string
            elseif (is_string($produkData) && !empty($produkData)) {
                $produkIds = explode(',', $produkData);
            }
        }
        
        // Remove empty values and convert to integers
        $produkIds = array_filter($produkIds, function($value) {
            return !empty(trim($value));
        });
        
        return array_map('intval', $produkIds);
    }
}