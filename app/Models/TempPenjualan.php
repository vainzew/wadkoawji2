<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPenjualan extends Model
{
    use HasFactory;

    protected $table = 'temp_penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $guarded = [];
    
    protected $fillable = [
        'id_member',
        'total_item',
        'total_harga',
        'diskon',
        'bayar',
        'diterima',
        'id_user',
        'metode_pembayaran',
        'status_pembayaran',
        'midtrans_order_id',
        'qr_code_url',
        'payment_expired_at'
    ];
    
    protected $casts = [
        'payment_expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Add indexes for frequently queried columns
    protected $indexes = [
        'id_user',
        'id_member',
        'created_at',
        'status_pembayaran',
        'metode_pembayaran'
    ];

    public function member()
    {
        return $this->hasOne(Member::class, 'id_member', 'id_member');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
    
    public function detail()
    {
        return $this->hasMany(TempPenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }
}