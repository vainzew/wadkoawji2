<?php

namespace App\Services;

use App\Models\Promo;
use App\Models\Produk;
use App\Models\TempPenjualanDetail;

class PromoService
{
    /**
     * Cek dan terapkan promo untuk produk yang ditambahkan
     */
    public function checkAndApplyPromo($idProduk, $quantity, $idPenjualan)
    {
        \Log::info('=== PromoService checkAndApplyPromo START ===', [
            'produk_id' => $idProduk,
            'quantity' => $quantity,
            'penjualan_id' => $idPenjualan
        ]);
        
        $produk = Produk::find($idProduk);
        if (!$produk) {
            \Log::warning('Produk not found:', ['id' => $idProduk]);
            return null;
        }

        // Cari promo aktif untuk produk ini
        $promos = $this->getActivePromoForProduct($idProduk);
        
        \Log::info('Active promos found:', [
            'count' => $promos->count(),
            'promos' => $promos->map(function($p) {
                return [
                    'id' => $p->id_promo,
                    'nama' => $p->nama_promo,
                    'tipe' => $p->tipe_promo,
                    'is_active' => $p->is_active,
                    'start_date' => $p->start_date,
                    'end_date' => $p->end_date
                ];
            })
        ]);
        
        $result = [];
        
        foreach ($promos as $promo) {
            switch ($promo->tipe_promo) {
                case 'percent_per_item':
                    $discount = $this->applyPercentDiscount($promo, $produk, $quantity);
                    if ($discount > 0) {
                        $result[] = [
                            'type' => 'discount',
                            'promo' => $promo,
                            'discount_amount' => $discount,
                            'description' => "Diskon {$promo->discount_percentage}% - {$promo->nama_promo}"
                        ];
                    }
                    break;
                    
                case 'b1g1_same_item':
                    $freeItems = $this->applyB1G1SameItem($promo, $produk, $quantity);
                    if ($freeItems > 0) {
                        $result[] = [
                            'type' => 'free_item',
                            'promo' => $promo,
                            'free_quantity' => $freeItems,
                            'free_product' => $produk,
                            'description' => "Buy 1 Get 1 Free - {$promo->nama_promo}"
                        ];
                    }
                    break;
                    
                case 'buy_a_get_b_free':
                    $freeItems = $this->applyBuyAGetBFree($promo, $idProduk, $quantity, $idPenjualan);
                    if ($freeItems['free_quantity'] > 0) {
                        $result[] = [
                            'type' => 'free_different_item',
                            'promo' => $promo,
                            'free_quantity' => $freeItems['free_quantity'],
                            'free_product' => $freeItems['free_product'],
                            'description' => "Beli {$promo->buy_quantity} {$produk->nama_produk} Gratis {$promo->get_quantity} {$freeItems['free_product']->nama_produk}"
                        ];
                    } else {
                        // Check if this could be a promo suggestion
                        $totalQuantity = $this->getTotalQuantityInTransaction($idPenjualan, $idProduk) + $quantity;
                        if ($totalQuantity < $promo->buy_quantity) {
                            $stillNeed = $promo->buy_quantity - $totalQuantity;
                            $freeProduct = Produk::find($promo->id_produk_get);
                            
                            if ($freeProduct) {
                                $result[] = [
                                    'type' => 'promo_suggestion',
                                    'promo' => $promo,
                                    'suggestion_message' => "Beli {$stillNeed} lagi dapat gratis {$promo->get_quantity} {$freeProduct->nama_produk}",
                                    'still_need' => $stillNeed,
                                    'free_product' => $freeProduct
                                ];
                            }
                        }
                    }
                    break;
            }
        }
        
        return $result;
    }

    /**
     * Dapatkan promo aktif untuk produk tertentu
     */
    private function getActivePromoForProduct($idProduk)
    {
        // Use cache to avoid repeated database queries for promos
        $cacheKey = 'product_promos_' . $idProduk . '_' . date('Y-m-d');
        $cachedPromos = cache($cacheKey);
        
        if ($cachedPromos !== null) {
            return $cachedPromos;
        }
        
        \Log::info('=== getActivePromoForProduct START ===', ['produk_id' => $idProduk]);
        
        $promos = collect();
        
        try {
            // Promo percent per item dan B1G1 same item
            $productPromos = Promo::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->whereIn('tipe_promo', ['percent_per_item', 'b1g1_same_item'])
                ->whereHas('produk', function($query) use ($idProduk) {
                    $query->where('produk.id_produk', $idProduk); // Fix ambiguous column
                })
                ->get();
            
            \Log::info('Product promos (percent/b1g1):', [
                'count' => $productPromos->count(),
                'promos' => $productPromos->map(function($p) {
                    return ['id' => $p->id_promo, 'nama' => $p->nama_promo, 'tipe' => $p->tipe_promo];
                })
            ]);
            
            $promos = $promos->merge($productPromos);
            
            // Promo buy A get B free
            $buyAGetBPromos = Promo::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('tipe_promo', 'buy_a_get_b_free')
                ->where('id_produk_buy', $idProduk)
                ->get();
            
            \Log::info('Buy A Get B promos:', [
                'count' => $buyAGetBPromos->count(),
                'promos' => $buyAGetBPromos->map(function($p) {
                    return ['id' => $p->id_promo, 'nama' => $p->nama_promo, 'tipe' => $p->tipe_promo];
                })
            ]);
            
            $promos = $promos->merge($buyAGetBPromos);
            
            // Cache promos for 1 hour to reduce database queries
            cache([$cacheKey => $promos], now()->addHour());
            
        } catch (\Exception $e) {
            \Log::warning('Error getting active promos: ' . $e->getMessage());
            return collect();
        }
        
        \Log::info('=== getActivePromoForProduct END ===', [
            'total_promos_found' => $promos->count(),
            'final_promos' => $promos->map(function($p) {
                return ['id' => $p->id_promo, 'nama' => $p->nama_promo, 'tipe' => $p->tipe_promo];
            })
        ]);
        
        return $promos;
    }

    /**
     * Terapkan diskon persen
     */
    private function applyPercentDiscount($promo, $produk, $quantity)
    {
        $discountPerItem = ($produk->harga_jual * $promo->discount_percentage / 100);
        return $discountPerItem * $quantity;
    }

    /**
     * Terapkan B1G1 same item
     */
    private function applyB1G1SameItem($promo, $produk, $quantity)
    {
        // Buy 1 Get 1: setiap 1 item yang dibeli, dapat 1 gratis
        \Log::info('B1G1 calculation:', [
            'quantity_purchased' => $quantity,
            'free_items' => $quantity
        ]);
        return $quantity; // 1:1 ratio - buy 1 get 1 free
    }

    /**
     * Terapkan Buy A Get B Free
     */
    private function applyBuyAGetBFree($promo, $idProduk, $quantity, $idPenjualan)
    {
        \Log::info('=== applyBuyAGetBFree START ===', [
            'promo_id' => $promo->id_promo,
            'promo_name' => $promo->nama_promo,
            'produk_id' => $idProduk,
            'quantity' => $quantity,
            'buy_quantity_required' => $promo->buy_quantity,
            'get_quantity' => $promo->get_quantity,
            'id_produk_buy' => $promo->id_produk_buy,
            'id_produk_get' => $promo->id_produk_get
        ]);
        
        if ($idProduk != $promo->id_produk_buy) {
            \Log::info('Product ID mismatch:', [
                'scanned_product' => $idProduk,
                'required_product' => $promo->id_produk_buy
            ]);
            return ['free_quantity' => 0, 'free_product' => null];
        }

        // Get total quantity dari semua item yang sama di transaksi ini
        $totalQuantity = $this->getTotalQuantityInTransaction($idPenjualan, $idProduk);
        $totalQuantity += $quantity; // Tambah quantity yang baru ditambah
        
        \Log::info('Total quantity calculation:', [
            'existing_quantity' => $totalQuantity - $quantity,
            'new_quantity' => $quantity,
            'total_quantity' => $totalQuantity
        ]);

        // ONLY trigger promo if we reach the minimum required quantity
        if ($totalQuantity < $promo->buy_quantity) {
            \Log::info('Minimum quantity not reached yet:', [
                'total_quantity' => $totalQuantity,
                'required_quantity' => $promo->buy_quantity,
                'still_need' => $promo->buy_quantity - $totalQuantity
            ]);
            return ['free_quantity' => 0, 'free_product' => null];
        }

        $freeQuantity = intval($totalQuantity / $promo->buy_quantity) * $promo->get_quantity;
        $freeProduct = Produk::find($promo->id_produk_get);
        
        \Log::info('Free quantity calculation:', [
            'total_quantity' => $totalQuantity,
            'buy_quantity_required' => $promo->buy_quantity,
            'sets_completed' => intval($totalQuantity / $promo->buy_quantity),
            'get_quantity_per_set' => $promo->get_quantity,
            'final_free_quantity' => $freeQuantity,
            'free_product_name' => $freeProduct ? $freeProduct->nama_produk : 'null'
        ]);
        
        return [
            'free_quantity' => $freeQuantity,
            'free_product' => $freeProduct
        ];
    }

    /**
     * Tambahkan item gratis ke penjualan detail
     */
    public function addFreeItemToTransaction($idPenjualan, $freeProduct, $freeQuantity, $promo, $description)
    {
        \Log::info('=== addFreeItemToTransaction START ===', [
            'penjualan_id' => $idPenjualan,
            'free_product_id' => $freeProduct ? $freeProduct->id_produk : null,
            'free_product_name' => $freeProduct ? $freeProduct->nama_produk : null,
            'free_quantity' => $freeQuantity,
            'promo_id' => $promo ? $promo->id_promo : null,
            'description' => $description
        ]);
        
        try {
            if ($freeQuantity <= 0 || !$freeProduct || !$promo) {
                \Log::warning('Invalid free item data:', [
                    'free_quantity' => $freeQuantity,
                    'free_product_exists' => !is_null($freeProduct),
                    'promo_exists' => !is_null($promo)
                ]);
                return null;
            }

            // Check if free item already exists untuk avoid duplicate
            $existingFreeItem = TempPenjualanDetail::where('id_penjualan', $idPenjualan)
                ->where('id_produk', $freeProduct->id_produk)
                ->where('id_promo', $promo->id_promo)
                ->where('is_free_item', true)
                ->first();
                
            if ($existingFreeItem) {
                \Log::info('Free item already exists, checking if update needed:', [
                    'existing_id' => $existingFreeItem->id_penjualan_detail,
                    'current_quantity' => $existingFreeItem->jumlah,
                    'required_quantity' => $freeQuantity
                ]);

                // Pastikan harga_jual pada item gratis menyimpan harga asli produk
                // (untuk keperluan GROSS, reporting, dan tampilan harga)
                if ((int) $existingFreeItem->harga_jual === 0 || $existingFreeItem->harga_jual != $freeProduct->harga_jual) {
                    $existingFreeItem->harga_jual = $freeProduct->harga_jual;
                }
                // Subtotal item gratis harus tetap 0
                if ((int) $existingFreeItem->subtotal !== 0) {
                    $existingFreeItem->subtotal = 0;
                }
                // Pastikan label GRATIS konsisten
                if (strpos($existingFreeItem->nama_produk ?? '', '(GRATIS)') === false) {
                    $existingFreeItem->nama_produk = ($freeProduct->nama_produk ?? 'Produk') . ' (GRATIS)';
                }
                
                // For Buy X Get Y, set the exact quantity needed, not add
                if ($promo->tipe_promo == 'buy_a_get_b_free') {
                    if ($existingFreeItem->jumlah != $freeQuantity) {
                        $existingFreeItem->jumlah = $freeQuantity;
                        $existingFreeItem->save();
                        \Log::info('Updated free item quantity:', [
                            'new_quantity' => $freeQuantity
                        ]);
                    }
                } else {
                    // For B1G1, add the quantity
                    $existingFreeItem->jumlah += $freeQuantity;
                    $existingFreeItem->save();
                    \Log::info('Added to free item quantity:', [
                        'added_quantity' => $freeQuantity,
                        'total_quantity' => $existingFreeItem->jumlah
                    ]);
                }

                return $existingFreeItem;
            }

            $freeItem = TempPenjualanDetail::create([
                'id_penjualan' => $idPenjualan,
                'id_produk' => $freeProduct->id_produk,
                'nama_produk' => $freeProduct->nama_produk . ' (GRATIS)',
                // Simpan harga asli untuk kebutuhan gross/reporting
                'harga_jual' => $freeProduct->harga_jual,
                'jumlah' => $freeQuantity,
                'diskon' => 0,
                // Gratis: subtotal tetap 0
                'subtotal' => 0,
                'id_promo' => $promo->id_promo,
                'promo_description' => $description,
                'is_free_item' => true
            ]);
            
            \Log::info('Free item created successfully:', [
                'free_item_id' => $freeItem->id_penjualan_detail,
                'product_name' => $freeItem->nama_produk,
                'quantity' => $freeItem->jumlah,
                'harga_jual' => $freeItem->harga_jual,
                'subtotal' => $freeItem->subtotal
            ]);
            
            return $freeItem;
        } catch (\Exception $e) {
            \Log::error('Error adding free item: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Update penjualan detail dengan informasi promo
     */
    public function updatePenjualanDetailWithPromo($penjualanDetailId, $promo, $description, $discountAmount = 0)
    {
        try {
            $penjualanDetail = TempPenjualanDetail::find($penjualanDetailId);
            if (!$penjualanDetail || !$promo) {
                return null;
            }

            // Calculate proper discount percentage
            $discountPercent = ($discountAmount / $penjualanDetail->harga_jual) * 100;
            $currentDiskon = $penjualanDetail->diskon;
            $totalDiskon = $currentDiskon + $discountPercent;
            
            // Calculate new subtotal with both product discount and promo discount
            $newSubtotal = $penjualanDetail->harga_jual * $penjualanDetail->jumlah;
            $newSubtotal = $newSubtotal - (($totalDiskon / 100) * $newSubtotal);

            // Update dengan informasi promo
            $penjualanDetail->update([
                'id_promo' => $promo->id_promo,
                'promo_description' => $description,
                'diskon' => $totalDiskon, // Total diskon (produk + promo)
                'subtotal' => $newSubtotal
            ]);

            return $penjualanDetail;
        } catch (\Exception $e) {
            \Log::warning('Error updating penjualan detail with promo: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get total quantity of a product in current transaction (excluding free items)
     */
    private function getTotalQuantityInTransaction($idPenjualan, $idProduk)
    {
        // Use cache to avoid repeated queries for the same transaction
        $cacheKey = 'transaction_qty_' . $idPenjualan . '_' . $idProduk;
        $cachedTotal = cache($cacheKey);
        
        if ($cachedTotal !== null) {
            return $cachedTotal;
        }
        
        $total = TempPenjualanDetail::where('id_penjualan', $idPenjualan)
            ->where('id_produk', $idProduk)
            ->where('is_free_item', false) // Exclude free items
            ->sum('jumlah');
            
        // Cache for 5 minutes - transaction quantities can change
        cache([$cacheKey => $total], now()->addMinutes(5));
            
        \Log::info('Total quantity in transaction:', [
            'penjualan_id' => $idPenjualan,
            'produk_id' => $idProduk,
            'total_quantity' => $total
        ]);
        
        return $total;
    }
}
