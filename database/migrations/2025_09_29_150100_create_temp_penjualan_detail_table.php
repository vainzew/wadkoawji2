<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempPenjualanDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_penjualan_detail', function (Blueprint $table) {
            $table->increments('id_penjualan_detail');
            $table->integer('id_penjualan');
            $table->integer('id_produk');
            $table->string('nama_produk')->nullable();
            $table->integer('harga_jual');
            $table->integer('jumlah');
            $table->tinyInteger('diskon')->default(0);
            $table->unsignedInteger('id_promo')->nullable();
            $table->string('promo_description')->nullable();
            $table->boolean('is_free_item')->default(false);
            $table->integer('subtotal');
            $table->timestamps();
            
            $table->foreign('id_promo')->references('id_promo')->on('promo')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_penjualan_detail');
    }
}