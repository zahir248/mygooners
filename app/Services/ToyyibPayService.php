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
    public function createBill($orderOrCheckoutData, $returnUrl = null, $isRetryPayment = false)
    {
        try {
            $httpClient = Http::asForm();
            
            // Disable SSL verification in development environment
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            // Use provided return URL or default to regular checkout
            $returnUrl = $returnUrl ?: route('checkout.toyyibpay.return');
            
            // Handle both order objects and checkout data arrays
            if (is_array($orderOrCheckoutData)) {
                // This is checkout data (new flow - no order created yet)
                $checkoutData = $orderOrCheckoutData;
                $orderNumber = 'PENDING_' . time() . '_' . auth()->id();
                $total = $checkoutData['total'];
                $shippingName = $checkoutData['shipping_data']['shipping_name'];
                $shippingEmail = $checkoutData['shipping_data']['shipping_email'];
                $shippingPhone = $checkoutData['shipping_data']['shipping_phone'];
                $orderId = null;
            } else {
                // This is an order object (existing flow)
                $order = $orderOrCheckoutData;
                $orderNumber = $order->order_number;
                $total = $order->total;
                $shippingName = $order->shipping_name;
                $shippingEmail = $order->shipping_email;
                $shippingPhone = $order->shipping_phone;
                $orderId = $order->id;
            }
            
            $requestData = [
                'userSecretKey' => $this->userSecretKey,
                'categoryCode' => $this->categoryCode,
                'billName' => 'Pesanan #' . $orderNumber,
                'billDescription' => 'Pembayaran untuk pesanan ' . $orderNumber,
                'billPriceSetting' => 1, // Fixed amount
                'billPayorInfo' => 1, // Collect customer info
                'billAmount' => $total * 100, // Convert to cents
                'billReturnUrl' => $returnUrl,
                'billCallbackUrl' => route('checkout.toyyibpay.callback'),
                'billExternalReferenceNo' => $orderNumber,
                'billTo' => $shippingName,
                'billEmail' => $shippingEmail,
                'billPhone' => $shippingPhone,
                'billSplitPayment' => 0,
                'billSplitPaymentArgs' => '',
                'billPaymentChannel' => 0, // All channels
                'billDisplayMerchant' => 1,
                'billContentEmail' => 'Terima kasih atas pembelian anda. Pesanan #' . $orderNumber,
                'billChargeToCustomer' => 0,
            ];

            Log::info('ToyyibPay API request', [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'url' => $this->baseUrl . '/index.php/api/createBill',
                'data' => $requestData
            ]);

            $response = $httpClient->post($this->baseUrl . '/index.php/api/createBill', $requestData);

            Log::info('ToyyibPay API response', [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'status' => $response->status(),
                'body' => $response->body(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('ToyyibPay API response data', [
                    'order_id' => $orderId,
                    'order_number' => $orderNumber,
                    'data' => $data
                ]);
                
                if (isset($data[0]['BillCode'])) {
                    // Only update order with bill code if this is NOT a retry payment and we have an order
                    if (!$isRetryPayment && $orderId) {
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
                        Log::info('ToyyibPay bill created - not updating order yet', [
                            'order_id' => $orderId,
                            'order_number' => $orderNumber,
                            'bill_code' => $data[0]['BillCode'],
                            'is_retry_payment' => $isRetryPayment,
                            'has_order' => $orderId ? true : false
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
                'order_id' => $orderId,
                'order_number' => $orderNumber,
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
     * Reuse existing bill code for retry payment
     */
    public function reuseBill($billCode, $order)
    {
        try {
            // First, verify if the bill exists and can be reused
            $verificationResult = $this->verifyPayment($billCode);
            
            if ($verificationResult['success']) {
                $status = $verificationResult['status'];
                
                // Only reuse if status is "0" (unpaid/pending)
                // Status "1" = paid, "3" = cancelled/failed, "4" = other failed states
                if ($status === '0') {
                    Log::info('Reusing existing ToyyibPay bill for retry', [
                        'order_number' => $order->order_number,
                        'bill_code' => $billCode,
                        'status' => $status
                    ]);

                    return [
                        'success' => true,
                        'bill_code' => $billCode,
                        'payment_url' => $this->baseUrl . '/' . $billCode,
                        'reused' => true
                    ];
                } else {
                    // Bill is paid, cancelled, or failed - create a new one
                    Log::info('Bill cannot be reused (paid/cancelled/failed), creating new bill', [
                        'bill_code' => $billCode,
                        'status' => $status,
                        'paid' => $verificationResult['paid']
                    ]);
                    
                    return $this->createBill($order, null, true);
                }
            } else {
                // Bill verification failed, create a new one
                Log::info('Bill verification failed, creating new bill', [
                    'bill_code' => $billCode,
                    'error' => $verificationResult['message']
                ]);
                
                return $this->createBill($order, null, true);
            }

        } catch (\Exception $e) {
            Log::error('Failed to reuse bill, creating new one', [
                'bill_code' => $billCode,
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);

            // If we can't verify the bill, create a new one
            return $this->createBill($order, null, true);
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
                        'paid' => $status === '1', // 1 = paid, 0 = unpaid, 3 = cancelled/failed
                        'amount' => $data[0]['billAmount'] ?? 0,
                        'paid_amount' => $data[0]['billpaymentAmount'] ?? 0,
                        'paid_date' => $data[0]['billPaymentDate'] ?? null,
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