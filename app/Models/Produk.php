<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $guarded = []; // Ini sudah cukup, semua field bisa diisi
    
    // Add indexes for frequently queried columns
    protected $indexes = [
        'id_kategori',
        'barcode',
        'stok',
        'harga_jual'
    ];

    // TIDAK PERLU cast ke date karena expired_at adalah string MM/YY
    // protected $casts = [
    //     'expired_at' => 'date',
    // ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    // Relationship dengan promo (many-to-many)
    public function promo()
    {
        return $this->belongsToMany(Promo::class, 'promo_produk', 'id_produk', 'id_promo');
    }

    // Helper method untuk mendapatkan promo yang aktif
    public function getActivePromo()
    {
        return $this->promo()->active()->first();
    }
}