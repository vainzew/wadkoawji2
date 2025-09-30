<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Ini sudah ada, bagus

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // === TAMBAHKAN BARIS INI UNTUK MENGHAPUS DATA LAMA ===
        // Karena tabel 'setting' biasanya hanya punya satu baris (dengan id_setting = 1),
        // kita bisa langsung truncate (mengosongkan) tabelnya.
        // HATI-HATI: Ini akan menghapus SEMUA data di tabel 'setting'.
        DB::table('setting')->truncate();
        // ====================================================

        DB::table('setting')->insert([
            'id_setting' => 1,
            'nama_perusahaan' => 'Toko Ku',
            'alamat' => 'Jl. Kibandang Samaran Ds. Slangit',
            'telepon' => '081234779987',
            'tipe_nota' => 1, // kecil
            'diskon' => 5,
            'path_logo' => '/img/logo.png',
            'path_kartu_member' => '/img/member.png',
        ]);
    }
}