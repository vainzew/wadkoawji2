<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        return view('setting.index');
    }

    public function show()
    {
        $setting = Setting::first();
        return response()->json([
            'status' => 'success',
            'data' => $setting
        ], 200);
    }

    public function update(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'nama_perusahaan' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            // 'diskon' akan digunakan sebagai Pajak (%)
            'diskon' => 'nullable|numeric|min:0|max:100',
            'tax_enabled' => 'nullable|boolean',
            'tipe_nota' => 'required|in:1,2',
            'path_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'path_kartu_member' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the first setting record or create a new one if it doesn't exist
        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
            // Set default values for new records
            $setting->path_logo = '/img/logo.png';
            $setting->path_kartu_member = '/img/member.png';
        }

        // Update the setting fields
        $setting->nama_perusahaan = $request->nama_perusahaan;
        $setting->telepon = $request->telepon;
        $setting->alamat = $request->alamat;
        // diskon dipakai sebagai Pajak (%)
        $setting->diskon = $request->diskon ?? 0; // Pajak (%)
        $setting->tax_enabled = $request->boolean('tax_enabled', false);
        $setting->tipe_nota = $request->tipe_nota;

        // Handle logo upload
        if ($request->hasFile('path_logo')) {
            $file = $request->file('path_logo');
            $nama = 'logo-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $nama);
            $setting->path_logo = "/img/$nama";
        }

        // Handle member card upload
        if ($request->hasFile('path_kartu_member')) {
            $file = $request->file('path_kartu_member');
            $nama = 'kartu-member-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $nama);
            $setting->path_kartu_member = "/img/$nama";
        }

        // Save the setting
        $setting->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $setting
        ], 200);
    }
}
