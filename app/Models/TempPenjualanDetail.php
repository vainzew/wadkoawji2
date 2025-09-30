<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPenjualanDetail extends Model
{
    use HasFactory;

    protected $table = 'temp_penjualan_detail';
    protected $primaryKey = 'id_penjualan_detail';
    protected $guarded = [];
    
    // Add indexes for frequently queried columns
    protected $indexes = [
        'id_penjualan',
        'id_produk',
        'id_promo',
        'is_free_item',
        'created_at'
    ];

    public function produk()
    {
        return $this->hasOne(Produk::class, 'id_produk', 'id_produk');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'id_promo', 'id_promo');
    }
    
    public function penjualan()
    {
        return $this->belongsTo(TempPenjualan::class, 'id_penjualan', 'id_penjualan');
    }
}