<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrisPaymentColumnsToPenjualanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['CASH', 'QRIS'])->default('CASH')->after('diterima');
            $table->enum('status_pembayaran', ['LUNAS', 'PENDING', 'DIBATALKAN', 'GAGAL'])->default('LUNAS')->after('metode_pembayaran');
            $table->string('midtrans_order_id')->nullable()->after('status_pembayaran');
            $table->text('qr_code_url')->nullable()->after('midtrans_order_id');
            $table->timestamp('payment_expired_at')->nullable()->after('qr_code_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn([
                'metode_pembayaran',
                'status_pembayaran', 
                'midtrans_order_id',
                'qr_code_url',
                'payment_expired_at'
            ]);
        });
    }
}
