<?php

namespace App\Services;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $botToken;
    protected $chatId;
    protected $apiUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN', '');
        $this->chatId = env('TELEGRAM_CHAT_ID', '');
    }

    public function sendTransactionNotification(Penjualan $penjualan)
    {
        // Jika token atau chat ID tidak dikonfigurasi, keluar dari fungsi
        if (empty($this->botToken) || empty($this->chatId)) {
            return false;
        }

        $message = $this->formatTransactionMessage($penjualan);
        
        $response = Http::post($this->apiUrl . $this->botToken . '/sendMessage', [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ]);
        
        return $response->successful();
    }

    private function formatTransactionMessage(Penjualan $penjualan)
    {
        // Ambil detail penjualan
        $details = PenjualanDetail::with('produk')
            ->where('id_penjualan', $penjualan->id_penjualan)
            ->get();
        
        // Format pesan
        $message = "🛒 <b>TRANSAKSI BARU #" . $penjualan->id_penjualan . "</b> 🛒\n";
        $message .= "Tanggal: " . tanggal_indonesia($penjualan->created_at, false) . " " . date('H:i', strtotime($penjualan->created_at)) . "\n";
        
        // Tambahkan info member jika ada
        if ($penjualan->id_member) {
            $message .= "Member: " . ($penjualan->member->nama ?? '-') . "\n";
        }
        
        $message .= "\n<b>Daftar Produk:</b>\n";
        
        foreach ($details as $item) {
            $message .= "- " . $item->produk->nama_produk;
            $message .= " (" . format_uang($item->jumlah) . " x Rp " . format_uang($item->harga_jual) . ")";
            $message .= " = Rp " . format_uang($item->subtotal) . "\n";
        }
        
        // Informasi total dan pembayaran
        $message .= "\n<b>Total Item:</b> " . format_uang($penjualan->total_item) . "\n";
        
        if ($penjualan->diskon > 0) {
            $message .= "<b>Diskon:</b> " . $penjualan->diskon . "%\n";
        }
        
        $message .= "<b>Total Bayar:</b> Rp " . format_uang($penjualan->bayar) . "\n";
        $message .= "<b>Diterima:</b> Rp " . format_uang($penjualan->diterima) . "\n";
        $message .= "<b>Kembali:</b> Rp " . format_uang($penjualan->diterima - $penjualan->bayar) . "\n";
        
        $message .= "\n<i>Kasir: " . ($penjualan->user->name ?? '-') . "</i>";
        
        return $message;
    }
}