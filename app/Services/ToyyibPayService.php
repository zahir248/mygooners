<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyyibPayService
{
    protected $userSecretKey;
    protected $categoryCode;
    protected $baseUrl;

    public function __construct()
    {
        $this->userSecretKey = config('services.toyyibpay.user_secret_key');
        $this->categoryCode = config('services.toyyibpay.category_code');
        $this->baseUrl = config('services.toyyibpay.base_url', 'https://toyyibpay.com');
    }

    /**
     * Create a new bill in ToyyibPay
     */
    public function createBill($order, $returnUrl = null, $isRetryPayment = false)
    {
        try {
            $httpClient = Http::asForm();
            
            // Disable SSL verification in development environment
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            // Use provided return URL or default to regular checkout
            $returnUrl = $returnUrl ?: route('checkout.toyyibpay.return');
            
            $requestData = [
                'userSecretKey' => $this->userSecretKey,
                'categoryCode' => $this->categoryCode,
                'billName' => 'Pesanan #' . $order->order_number,
                'billDescription' => 'Pembayaran untuk pesanan ' . $order->order_number,
                'billPriceSetting' => 1, // Fixed amount
                'billPayorInfo' => 1, // Collect customer info
                'billAmount' => $order->total * 100, // Convert to cents
                'billReturnUrl' => $returnUrl,
                'billCallbackUrl' => route('checkout.toyyibpay.callback'),
                'billExternalReferenceNo' => $order->order_number,
                'billTo' => $order->shipping_name,
                'billEmail' => $order->shipping_email,
                'billPhone' => $order->shipping_phone,
                'billSplitPayment' => 0,
                'billSplitPaymentArgs' => '',
                'billPaymentChannel' => 0, // All channels
                'billDisplayMerchant' => 1,
                'billContentEmail' => 'Terima kasih atas pembelian anda. Pesanan #' . $order->order_number,
                'billChargeToCustomer' => 0,
            ];

            Log::info('ToyyibPay API request', [
                'order_id' => $order->id,
                'url' => $this->baseUrl . '/index.php/api/createBill',
                'data' => $requestData
            ]);

            $response = $httpClient->post($this->baseUrl . '/index.php/api/createBill', $requestData);

            Log::info('ToyyibPay API response', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('ToyyibPay API response data', [
                    'order_id' => $order->id,
                    'data' => $data
                ]);
                
                if (isset($data[0]['BillCode'])) {
                    // Only update order with bill code if this is NOT a retry payment
                    if (!$isRetryPayment) {
                        $order->update([
                            'toyyibpay_bill_code' => $data[0]['BillCode'],
                            'payment_status' => 'pending'
                        ]);

                        // Refresh the order to get the updated data
                        $order->refresh();

                        Log::info('ToyyibPay order updated with bill code', [
                            'order_id' => $order->id,
                            'bill_code' => $order->toyyibpay_bill_code,
                            'payment_status' => $order->payment_status
                        ]);
                    } else {
                        Log::info('ToyyibPay bill created for retry payment - not updating order yet', [
                            'order_id' => $order->id,
                            'bill_code' => $data[0]['BillCode'],
                            'is_retry_payment' => true
                        ]);
                    }

                    return [
                        'success' => true,
                        'bill_code' => $data[0]['BillCode'],
                        'payment_url' => $this->baseUrl . '/' . $data[0]['BillCode']
                    ];
                }
            }

            Log::error('ToyyibPay create bill failed', [
                'order_id' => $order->id,
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membuat bil pembayaran'
            ];

        } catch (\Exception $e) {
            Log::error('ToyyibPay create bill exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Ralat sistem pembayaran'
            ];
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment($billCode)
    {
        try {
            $httpClient = Http::asForm();
            
            // Disable SSL verification in development environment
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $requestData = [
                'userSecretKey' => $this->userSecretKey,
                'billCode' => $billCode,
            ];

            Log::info('ToyyibPay verify payment request', [
                'bill_code' => $billCode,
                'url' => $this->baseUrl . '/index.php/api/getBillTransactions',
                'data' => $requestData
            ]);

            $response = $httpClient->post($this->baseUrl . '/index.php/api/getBillTransactions', $requestData);

            Log::info('ToyyibPay verify payment response', [
                'bill_code' => $billCode,
                'status' => $response->status(),
                'body' => $response->body(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('ToyyibPay verify payment data', [
                    'bill_code' => $billCode,
                    'data' => $data
                ]);
                
                if (isset($data[0]['billpaymentStatus'])) {
                    $status = $data[0]['billpaymentStatus'];
                    
                    return [
                        'success' => true,
                        'status' => $status,
                        'paid' => $status === '1', // 1 = paid, 0 = unpaid
                        'amount' => $data[0]['billAmount'] ?? 0,
                        'paid_amount' => $data[0]['paidAmount'] ?? 0,
                        'paid_date' => $data[0]['paidDate'] ?? null,
                    ];
                }
            }

            Log::error('ToyyibPay verify payment failed', [
                'bill_code' => $billCode,
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengesahkan pembayaran ToyyibPay'
            ];

        } catch (\Exception $e) {
            Log::error('ToyyibPay verify payment exception', [
                'bill_code' => $billCode,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa mengesahkan pembayaran ToyyibPay'
            ];
        }
    }

    /**
     * Cancel a bill (Note: ToyyibPay doesn't have a direct cancel API, 
     * but we can mark it as inactive or handle it in our system)
     */
    public function cancelBill($billCode)
    {
        try {
            // Since ToyyibPay doesn't provide a direct cancel API,
            // we'll just log the cancellation attempt
            // In a real implementation, you might want to:
            // 1. Call ToyyibPay support to cancel the bill
            // 2. Mark the bill as inactive in your system
            // 3. Send a notification to the customer
            
            Log::info('ToyyibPay bill cancellation requested', [
                'bill_code' => $billCode,
                'note' => 'ToyyibPay does not provide a direct cancel API. Manual intervention may be required.'
            ]);

            return [
                'success' => true,
                'message' => 'Bill cancellation logged. Manual intervention may be required for ToyyibPay bills.'
            ];

        } catch (\Exception $e) {
            Log::error('ToyyibPay bill cancellation failed', [
                'bill_code' => $billCode,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membatalkan bil ToyyibPay: ' . $e->getMessage()
            ];
        }
    }
} 