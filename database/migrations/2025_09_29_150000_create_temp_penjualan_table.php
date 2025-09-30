<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempPenjualanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_penjualan', function (Blueprint $table) {
            $table->increments('id_penjualan');
            $table->integer('id_member')->nullable();
            $table->integer('total_item');
            $table->integer('total_harga');
            $table->tinyInteger('diskon')->default(0);
            $table->integer('bayar')->default(0);
            $table->integer('diterima')->default(0);
            $table->integer('id_user');
            $table->enum('metode_pembayaran', ['CASH', 'QRIS'])->default('CASH');
            $table->enum('status_pembayaran', ['LUNAS', 'PENDING', 'DIBATALKAN', 'GAGAL'])->default('LUNAS');
            $table->string('midtrans_order_id')->nullable();
            $table->text('qr_code_url')->nullable();
            $table->timestamp('payment_expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_penjualan');
    }
}