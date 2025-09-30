<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add indexes for temp_penjualan table
        Schema::table('temp_penjualan', function (Blueprint $table) {
            $table->index('id_user', 'idx_temp_penjualan_id_user');
            $table->index('id_member', 'idx_temp_penjualan_id_member');
            $table->index('created_at', 'idx_temp_penjualan_created_at');
            $table->index('status_pembayaran', 'idx_temp_penjualan_status_pembayaran');
            $table->index('metode_pembayaran', 'idx_temp_penjualan_metode_pembayaran');
        });

        // Add indexes for temp_penjualan_detail table
        Schema::table('temp_penjualan_detail', function (Blueprint $table) {
            $table->index('id_penjualan', 'idx_temp_penjualan_detail_id_penjualan');
            $table->index('id_produk', 'idx_temp_penjualan_detail_id_produk');
            $table->index('id_promo', 'idx_temp_penjualan_detail_id_promo');
            $table->index('is_free_item', 'idx_temp_penjualan_detail_is_free_item');
            $table->index('created_at', 'idx_temp_penjualan_detail_created_at');
        });

        // Add indexes for penjualan table
        Schema::table('penjualan', function (Blueprint $table) {
            $table->index('id_user', 'idx_penjualan_id_user');
            $table->index('id_member', 'idx_penjualan_id_member');
            $table->index('created_at', 'idx_penjualan_created_at');
            $table->index('status_pembayaran', 'idx_penjualan_status_pembayaran');
            $table->index('metode_pembayaran', 'idx_penjualan_metode_pembayaran');
        });

        // Add indexes for penjualan_detail table
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->index('id_penjualan', 'idx_penjualan_detail_id_penjualan');
            $table->index('id_produk', 'idx_penjualan_detail_id_produk');
            $table->index('id_promo', 'idx_penjualan_detail_id_promo');
            $table->index('created_at', 'idx_penjualan_detail_created_at');
        });

        // Add indexes for promo table
        Schema::table('promo', function (Blueprint $table) {
            $table->index('is_active', 'idx_promo_is_active');
            $table->index('start_date', 'idx_promo_start_date');
            $table->index('end_date', 'idx_promo_end_date');
            $table->index('tipe_promo', 'idx_promo_tipe_promo');
            $table->index('id_produk_buy', 'idx_promo_id_produk_buy');
            $table->index('id_produk_get', 'idx_promo_id_produk_get');
            
            // Composite index for active promos with date range
            $table->index(['is_active', 'start_date', 'end_date'], 'idx_promo_active_date_range');
        });

        // Add indexes for produk table
        Schema::table('produk', function (Blueprint $table) {
            $table->index('id_kategori', 'idx_produk_id_kategori');
            $table->index('barcode', 'idx_produk_barcode');
            $table->index('stok', 'idx_produk_stok');
            $table->index('harga_jual', 'idx_produk_harga_jual');
        });

        // Add indexes for member table
        Schema::table('member', function (Blueprint $table) {
            $table->index('nama', 'idx_member_nama');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop indexes for temp_penjualan table
        Schema::table('temp_penjualan', function (Blueprint $table) {
            $table->dropIndex('idx_temp_penjualan_id_user');
            $table->dropIndex('idx_temp_penjualan_id_member');
            $table->dropIndex('idx_temp_penjualan_created_at');
            $table->dropIndex('idx_temp_penjualan_status_pembayaran');
            $table->dropIndex('idx_temp_penjualan_metode_pembayaran');
        });

        // Drop indexes for temp_penjualan_detail table
        Schema::table('temp_penjualan_detail', function (Blueprint $table) {
            $table->dropIndex('idx_temp_penjualan_detail_id_penjualan');
            $table->dropIndex('idx_temp_penjualan_detail_id_produk');
            $table->dropIndex('idx_temp_penjualan_detail_id_promo');
            $table->dropIndex('idx_temp_penjualan_detail_is_free_item');
            $table->dropIndex('idx_temp_penjualan_detail_created_at');
        });

        // Drop indexes for penjualan table
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropIndex('idx_penjualan_id_user');
            $table->dropIndex('idx_penjualan_id_member');
            $table->dropIndex('idx_penjualan_created_at');
            $table->dropIndex('idx_penjualan_status_pembayaran');
            $table->dropIndex('idx_penjualan_metode_pembayaran');
        });

        // Drop indexes for penjualan_detail table
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->dropIndex('idx_penjualan_detail_id_penjualan');
            $table->dropIndex('idx_penjualan_detail_id_produk');
            $table->dropIndex('idx_penjualan_detail_id_promo');
            $table->dropIndex('idx_penjualan_detail_created_at');
        });

        // Drop indexes for promo table
        Schema::table('promo', function (Blueprint $table) {
            $table->dropIndex('idx_promo_is_active');
            $table->dropIndex('idx_promo_start_date');
            $table->dropIndex('idx_promo_end_date');
            $table->dropIndex('idx_promo_tipe_promo');
            $table->dropIndex('idx_promo_id_produk_buy');
            $table->dropIndex('idx_promo_id_produk_get');
            $table->dropIndex('idx_promo_active_date_range');
        });

        // Drop indexes for produk table
        Schema::table('produk', function (Blueprint $table) {
            $table->dropIndex('idx_produk_id_kategori');
            $table->dropIndex('idx_produk_barcode');
            $table->dropIndex('idx_produk_stok');
            $table->dropIndex('idx_produk_harga_jual');
        });

        // Drop indexes for member table
        Schema::table('member', function (Blueprint $table) {
            $table->dropIndex('idx_member_nama');
        });
    }
}