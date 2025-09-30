<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_produk', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_promo');
            $table->unsignedInteger('id_produk');
            $table->timestamps();
            
            $table->foreign('id_promo')->references('id_promo')->on('promo')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_produk');
    }
}