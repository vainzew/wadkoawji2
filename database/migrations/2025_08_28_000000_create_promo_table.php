<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo', function (Blueprint $table) {
            $table->increments('id_promo');
            $table->string('nama_promo');
            $table->enum('tipe_promo', ['percent_per_item', 'b1g1_same_item', 'buy_a_get_b_free']);
            $table->text('description')->nullable();
            
            // Untuk percent per item
            $table->decimal('discount_percentage', 5, 2)->nullable();
            
            // Untuk B1G1 dan Buy A Get B Free
            $table->integer('buy_quantity')->nullable(); // Jumlah yang harus dibeli
            $table->integer('get_quantity')->nullable(); // Jumlah yang didapat gratis
            $table->unsignedInteger('id_produk_buy')->nullable(); // Produk yang dibeli (untuk Buy A Get B)
            $table->unsignedInteger('id_produk_get')->nullable(); // Produk yang didapat gratis (untuk Buy A Get B)
            
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('promo');
    }
}