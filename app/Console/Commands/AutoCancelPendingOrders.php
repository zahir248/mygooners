<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\StripeService;
use App\Services\ToyyibPayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCancelPendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-cancel-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel orders that have been pending for more than 24 hours without payment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic cancellation of pending orders...');

        // Find orders that are pending and older than 24 hours
        $pendingOrders = Order::where('status', 'pending')
            ->whereIn('payment_status', ['pending', 'failed'])
            ->where('created_at', '<=', now()->subHours(24))
            ->get();

        $this->info("Found {$pendingOrders->count()} orders to cancel.");

        if ($pendingOrders->isEmpty()) {
            $this->info('No orders to cancel.');
            return 0;
        }

        $cancelledCount = 0;
        $errorCount = 0;

        foreach ($pendingOrders as $order) {
            try {
                DB::beginTransaction();

                $this->info("Processing order {$order->order_number} (ID: {$order->id})");

                // Cancel payment if it exists
                $this->cancelPayment($order);

                // Update order status to cancelled
                $order->update([
                    'status' => 'cancelled',
                    'notes' => $order->notes ? 
                        $order->notes . "\n\nDibatalkan secara automatik oleh sistem pada " . now()->format('d/m/Y H:i') . " kerana tidak dibayar dalam masa 24 jam" :
                        'Dibatalkan secara automatik oleh sistem pada ' . now()->format('d/m/Y H:i') . ' kerana tidak dibayar dalam masa 24 jam'
                ]);

                DB::commit();

                $cancelledCount++;
                $this->info("✓ Successfully cancelled order {$order->order_number}");

                Log::info('Order auto-cancelled', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                    'cancelled_at' => now(),
                    'reason' => 'Auto-cancelled due to non-payment after 24 hours'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                $errorCount++;

                $this->error("✗ Failed to cancel order {$order->order_number}: {$e->getMessage()}");

                Log::error('Failed to auto-cancel order', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Auto-cancellation completed:");
        $this->info("- Successfully cancelled: {$cancelledCount} orders");
        $this->info("- Errors: {$errorCount} orders");

        return 0;
    }

    /**
     * Cancel payment for the order if it exists
     */
    private function cancelPayment(Order $order)
    {
        // Cancel Stripe payment intent if it exists
        if ($order->stripe_payment_intent_id) {
            try {
                $stripeService = new StripeService();
                $stripeService->cancelPaymentIntent($order->stripe_payment_intent_id);
                
                $this->info("  - Cancelled Stripe payment intent: {$order->stripe_payment_intent_id}");
                
                Log::info('Stripe payment intent auto-cancelled', [
                    'order_id' => $order->id,
                    'payment_intent_id' => $order->stripe_payment_intent_id
                ]);
            } catch (\Exception $e) {
                $this->warn("  - Failed to cancel Stripe payment intent: {$e->getMessage()}");
                
                Log::warning('Failed to auto-cancel Stripe payment intent', [
                    'order_id' => $order->id,
                    'payment_intent_id' => $order->stripe_payment_intent_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Cancel ToyyibPay bill if it exists
        if ($order->toyyibpay_bill_code) {
            try {
                $toyyibpayService = new ToyyibPayService();
                $toyyibpayService->cancelBill($order->toyyibpay_bill_code);
                
                $this->info("  - Cancelled ToyyibPay bill: {$order->toyyibpay_bill_code}");
                
                Log::info('ToyyibPay bill auto-cancelled', [
                    'order_id' => $order->id,
                    'bill_code' => $order->toyyibpay_bill_code
                ]);
            } catch (\Exception $e) {
                $this->warn("  - Failed to cancel ToyyibPay bill: {$e->getMessage()}");
                
                Log::warning('Failed to auto-cancel ToyyibPay bill', [
                    'order_id' => $order->id,
                    'bill_code' => $order->toyyibpay_bill_code,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
} 