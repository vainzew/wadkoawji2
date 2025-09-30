<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;
use App\Services\TelegramService;
use App\Models\Penjualan;

class MidtransWebhookController extends Controller
{
    protected $midtransService;
    protected $telegramService;

    public function __construct(MidtransService $midtransService, TelegramService $telegramService)
    {
        $this->midtransService = $midtransService;
        $this->telegramService = $telegramService;
    }

    /**
     * Handle Midtrans webhook notification
     */
    public function handleNotification(Request $request)
    {
        try {
            Log::info('Midtrans webhook received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            $notification = $request->all();
            
            // Validate required fields
            if (!isset($notification['order_id']) || !isset($notification['transaction_status'])) {
                Log::warning('Invalid webhook payload - missing required fields');
                return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
            }

            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;

            Log::info('Processing webhook', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            // Find the penjualan record
            $penjualan = Penjualan::where('midtrans_order_id', $orderId)->first();
            
            if (!$penjualan) {
                Log::warning('Penjualan not found for order ID: ' . $orderId);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Determine the payment status based on Midtrans status
            $paymentStatus = $this->determinePaymentStatus($transactionStatus, $fraudStatus);
            
            Log::info('Updating payment status', [
                'order_id' => $orderId,
                'old_status' => $penjualan->status_pembayaran,
                'new_status' => $paymentStatus
            ]);

            // Update payment status
            $updated = $this->midtransService->updatePaymentStatus($orderId, $transactionStatus, $penjualan->id_penjualan);
            
            if ($updated) {
                // Refresh penjualan to get updated status
                $penjualan->refresh();
                
                // Send notification if payment is completed
                if ($penjualan->status_pembayaran === 'LUNAS') {
                    Log::info('Payment completed, sending notification');
                    $this->telegramService->sendTransactionNotification($penjualan);
                }
                
                Log::info('Webhook processed successfully', [
                    'order_id' => $orderId,
                    'final_status' => $penjualan->status_pembayaran
                ]);
                
                return response()->json(['status' => 'success'], 200);
            } else {
                Log::error('Failed to update payment status for order: ' . $orderId);
                return response()->json(['status' => 'error', 'message' => 'Failed to update status'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all()
            ]);
            
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Determine payment status based on Midtrans response
     */
    private function determinePaymentStatus($transactionStatus, $fraudStatus = null)
    {
        // Handle fraud status first
        if ($fraudStatus === 'deny') {
            return 'GAGAL';
        }

        // Map transaction status to our payment status
        switch ($transactionStatus) {
            case 'capture':
                // For credit card, check fraud status
                return ($fraudStatus === 'accept') ? 'LUNAS' : 'PENDING';
                
            case 'settlement':
                return 'LUNAS';
                
            case 'pending':
                return 'PENDING';
                
            case 'deny':
            case 'failure':
                return 'GAGAL';
                
            case 'cancel':
            case 'expire':
                return 'DIBATALKAN';
                
            default:
                Log::warning('Unknown transaction status: ' . $transactionStatus);
                return 'PENDING';
        }
    }

    /**
     * Handle payment success page (optional)
     */
    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if ($orderId) {
            $penjualan = Penjualan::where('midtrans_order_id', $orderId)->first();
            
            if ($penjualan) {
                return view('payment.success', compact('penjualan'));
            }
        }
        
        return redirect()->route('dashboard')->with('success', 'Payment completed successfully');
    }

    /**
     * Handle payment failure page (optional)
     */
    public function paymentFailed(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if ($orderId) {
            $penjualan = Penjualan::where('midtrans_order_id', $orderId)->first();
            
            if ($penjualan) {
                return view('payment.failed', compact('penjualan'));
            }
        }
        
        return redirect()->route('dashboard')->with('error', 'Payment failed');
    }
}