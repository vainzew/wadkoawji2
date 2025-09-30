<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifikasiStokProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produk', function (Blueprint $table) {
            // Tambah kolom stok_gudang terlebih dahulu
            $table->integer('stok_gudang')->after('stok')->default(0);
            
            // Rename kolom stok menjadi stok_display
            $table->renameColumn('stok', 'stok_display');
        });

        // Update data yang sudah ada
        DB::statement('UPDATE produk SET stok_gudang = stok_display');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk', function (Blueprint $table) {
            // Kembalikan nama kolom stok_display ke stok
            $table->renameColumn('stok_display', 'stok');
            
            // Hapus kolom stok_gudang
            $table->dropColumn('stok_gudang');
        });
    }
}