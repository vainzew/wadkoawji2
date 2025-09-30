<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample promo data
        $promos = [
            [
                'nama_promo' => 'Diskon 10% untuk Produk Elektronik',
                'tipe_promo' => 'percent_per_item',
                'description' => 'Promo khusus untuk semua produk elektronik dengan diskon 10%',
                'discount_percentage' => 10.00,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'nama_promo' => 'Buy 1 Get 1 Free - Snack',
                'tipe_promo' => 'b1g1_same_item',
                'description' => 'Beli 1 gratis 1 untuk produk snack',
                'start_date' => now(),
                'end_date' => now()->addDays(14),
                'is_active' => true,
            ],
            [
                'nama_promo' => 'Beli Air Mineral Gratis Biskuit',
                'tipe_promo' => 'buy_a_get_b_free',
                'description' => 'Beli 2 air mineral gratis 1 biskuit',
                'buy_quantity' => 2,
                'get_quantity' => 1,
                'start_date' => now(),
                'end_date' => now()->addDays(7),
                'is_active' => true,
            ],
        ];

        foreach ($promos as $promoData) {
            $promo = Promo::create($promoData);
            
            // Attach sample products for percent_per_item and b1g1_same_item
            if (in_array($promo->tipe_promo, ['percent_per_item', 'b1g1_same_item'])) {
                $sampleProducts = Produk::inRandomOrder()->take(3)->pluck('id_produk');
                if ($sampleProducts->isNotEmpty()) {
                    $promo->produk()->sync($sampleProducts);
                }
            }
            
            // Set specific products for buy_a_get_b_free
            if ($promo->tipe_promo == 'buy_a_get_b_free') {
                $products = Produk::inRandomOrder()->take(2)->pluck('id_produk');
                if ($products->count() >= 2) {
                    $promo->update([
                        'id_produk_buy' => $products[0],
                        'id_produk_get' => $products[1],
                    ]);
                }
            }
        }
    }
}