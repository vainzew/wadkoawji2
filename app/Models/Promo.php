<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promo';
    protected $primaryKey = 'id_promo';
    protected $guarded = [];
    
    // Add indexes for frequently queried columns
    protected $indexes = [
        'is_active',
        'start_date',
        'end_date',
        'tipe_promo',
        'id_produk_buy',
        'id_produk_get'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationship dengan produk (many-to-many)
    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'promo_produk', 'id_promo', 'id_produk');
    }

    // Relationship untuk Buy A Get B Free - produk yang dibeli
    public function produkBuy()
    {
        return $this->belongsTo(Produk::class, 'id_produk_buy', 'id_produk');
    }

    // Relationship untuk Buy A Get B Free - produk yang didapat gratis
    public function produkGet()
    {
        return $this->belongsTo(Produk::class, 'id_produk_get', 'id_produk');
    }

    // Scope untuk promo yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    // Helper method untuk mengecek apakah promo masih berlaku
    public function isValidPromo()
    {
        return $this->is_active && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    // Helper method untuk menghitung diskon
    public function calculateDiscount($harga_jual, $quantity = 1)
    {
        if (!$this->isValidPromo()) {
            return 0;
        }

        switch ($this->tipe_promo) {
            case 'percent_per_item':
                return ($harga_jual * $this->discount_percentage / 100) * $quantity;
            
            case 'b1g1_same_item':
                $free_quantity = intval($quantity / 2); // Setiap 2 item, 1 gratis
                return $harga_jual * $free_quantity;
            
            case 'buy_a_get_b_free':
                if ($quantity >= $this->buy_quantity) {
                    $free_sets = intval($quantity / $this->buy_quantity);
                    $produk_gratis = $this->produkGet;
                    if ($produk_gratis) {
                        return $produk_gratis->harga_jual * $free_sets * $this->get_quantity;
                    }
                }
                return 0;
            
            default:
                return 0;
        }
    }
}