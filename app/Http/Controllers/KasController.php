<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use Illuminate\Http\Request;

class KasController extends Controller
{
    public function index()
    {
        return view('kas.index');
    }

    public function data()
    {
        $kas = Kas::orderBy('id', 'desc')->get();

        $saldo = 0;
        foreach ($kas as $item) {
            $saldo += $item->nominal_setoran;
            $item->saldo = $saldo;
        }

        return datatables()
            ->of($kas)
            ->addIndexColumn()
            ->addColumn('created_at', function ($kas) {
                return tanggal_indonesia($kas->created_at, false);
            })
            ->addColumn('nominal_setoran', function ($kas) {
                return format_uang($kas->nominal_setoran);
            })
            ->addColumn('saldo', function ($kas) {
                return format_uang($kas->saldo);
            })
            ->addColumn('aksi', function ($kas) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('kas.update', $kas->id) .'`)" class="btn-edit btn-icon" data-bs-toggle="tooltip" data-bs-placement="left" title="Edit"><i class="mynaui-edit"></i></button>
                    <button type="button" onclick="deleteData(`'. route('kas.destroy', $kas->id) .'`)" class="btn-delete btn-icon" data-bs-toggle="tooltip" data-bs-placement="right" title="Hapus"><i class="mynaui-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $kas = Kas::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $kas
        ], 200);
    }

    public function show($id)
    {
        $kas = Kas::find($id);

        return response()->json([
            'status' => 'success',
            'data' => $kas
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $kas = Kas::find($id);
        $kas->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $kas
        ], 200);
    }

    public function destroy($id)
    {
        $kas = Kas::find($id);
        $kas->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ], 200);
    }
}