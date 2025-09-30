<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNamaProdukColumnToPenjualanDetailTable extends Migration
{
    public function up()
    {
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->string('nama_produk')->nullable()->after('id_produk');
        });
    }

    public function down()
    {
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->dropColumn('nama_produk');
        });
    }
}