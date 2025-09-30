<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromoColumnToPenjualanDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->unsignedInteger('id_promo')->nullable()->after('diskon');
            $table->string('promo_description')->nullable()->after('id_promo');
            $table->boolean('is_free_item')->default(false)->after('promo_description');
            
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
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->dropForeign(['id_promo']);
            $table->dropColumn(['id_promo', 'promo_description', 'is_free_item']);
        });
    }
}