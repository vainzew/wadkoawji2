<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\CoreApi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Penjualan;
use Carbon\Carbon;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        Config::$isProduction = config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        Config::$isSanitized = config('services.midtrans.is_sanitized', env('MIDTRANS_IS_SANITIZED', true));
        Config::$is3ds = config('services.midtrans.is_3ds', env('MIDTRANS_IS_3DS', true));
    }

    /**
     * Create QRIS payment using Core API
     */
    public function createQrisPayment($orderId, $grossAmount, $customerDetails = null)
    {
        try {
            $params = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'qris' => [
                    'acquirer' => 'gopay'
                ]
            ];

            // Add customer details if provided
            if ($customerDetails) {
                $params['customer_details'] = $customerDetails;
            }

            Log::info('Creating QRIS payment', ['params' => $params]);

            $response = CoreApi::charge($params);
            
            Log::info('Midtrans QRIS response', ['response' => $response]);

            return [
                'success' => true,
                'data' => $response
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans QRIS Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create GoPay payment using Core API
     */
    public function createGopayPayment($orderId, $grossAmount, $customerDetails = null)
    {
        try {
            $params = [
                'payment_type' => 'gopay',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'gopay' => [
                    'enable_callback' => true,
                    'callback_url' => config('app.url') . '/payment/callback'
                ]
            ];

            // Add customer details if provided
            if ($customerDetails) {
                $params['customer_details'] = $customerDetails;
            }

            Log::info('Creating GoPay payment', ['params' => $params]);

            $response = CoreApi::charge($params);
            
            Log::info('Midtrans GoPay response', ['response' => $response]);

            return [
                'success' => true,
                'data' => $response
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans GoPay Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create Bank Transfer payment using Core API
     */
    public function createBankTransferPayment($orderId, $grossAmount, $customerDetails = null)
    {
        try {
            $params = [
                'payment_type' => 'bank_transfer',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'bank_transfer' => [
                    'bank' => 'bca', // Default ke BCA, bisa diganti
                ]
            ];

            // Add customer details if provided
            if ($customerDetails) {
                $params['customer_details'] = $customerDetails;
            }

            Log::info('Creating Bank Transfer payment', ['params' => $params]);

            $response = CoreApi::charge($params);
            
            Log::info('Midtrans Bank Transfer response', ['response' => $response]);

            return [
                'success' => true,
                'data' => $response
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Bank Transfer Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($orderId)
    {
        try {
            $response = CoreApi::status($orderId);
            
            Log::info('Payment status check', ['order_id' => $orderId, 'response' => $response]);
            
            return [
                'success' => true,
                'data' => $response
            ];

        } catch (\Exception $e) {
            Log::error('Check payment status error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Cancel payment
     */
    public function cancelPayment($orderId)
    {
        try {
            $response = CoreApi::cancel($orderId);
            
            Log::info('Payment cancelled', ['order_id' => $orderId, 'response' => $response]);
            
            return [
                'success' => true,
                'data' => $response
            ];

        } catch (\Exception $e) {
            Log::error('Cancel payment error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate unique order ID
     */
    public function generateOrderId($prefix = 'ORDER')
    {
        return $prefix . '-' . date('YmdHis') . '-' . Str::random(6);
    }

    /**
     * Update payment status in database
     */
    public function updatePaymentStatus($orderId, $status, $penjualanId = null)
    {
        try {
            $statusMapping = [
                'pending' => 'PENDING',
                'settlement' => 'LUNAS',
                'capture' => 'LUNAS',
                'deny' => 'GAGAL',
                'cancel' => 'DIBATALKAN',
                'expire' => 'DIBATALKAN',
                'failure' => 'GAGAL'
            ];

            $mappedStatus = $statusMapping[$status] ?? 'PENDING';

            // Find penjualan by order_id or id
            if ($penjualanId) {
                $penjualan = Penjualan::find($penjualanId);
            } else {
                $penjualan = Penjualan::where('midtrans_order_id', $orderId)->first();
            }

            if ($penjualan) {
                $penjualan->update([
                    'status_pembayaran' => $mappedStatus
                ]);

                Log::info('Payment status updated', [
                    'order_id' => $orderId,
                    'status' => $mappedStatus,
                    'penjualan_id' => $penjualan->id_penjualan
                ]);

                return true;
            }

            Log::warning('Penjualan not found for order ID: ' . $orderId);
            return false;

        } catch (\Exception $e) {
            Log::error('Update payment status error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get QR code string from Midtrans response
     */
    public function getQrCodeUrl($midtransResponse)
    {
        if (isset($midtransResponse->actions)) {
            foreach ($midtransResponse->actions as $action) {
                if ($action->name === 'generate-qr-code') {
                    return $action->url;
                }
            }
        }
        
        return null;
    }

    /**
     * Set payment expiry (default 30 minutes)
     */
    public function getPaymentExpiry($minutes = 30)
    {
        return Carbon::now()->addMinutes($minutes);
    }
}