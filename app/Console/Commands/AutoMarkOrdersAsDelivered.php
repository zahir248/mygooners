<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoMarkOrdersAsDelivered extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-mark-delivered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark shipped orders as delivered after 7 days if not manually marked by user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic order status update...');

        // Find orders that are shipped and have been shipped for more than 7 days
        // and haven't been manually marked as delivered by the user
        $ordersToUpdate = Order::where('status', 'shipped')
            ->whereNotNull('shipped_at')
            ->where('shipped_at', '<=', now()->subDays(7))
            ->whereNull('delivered_at')
            ->get();

        $updatedCount = 0;

        foreach ($ordersToUpdate as $order) {
            try {
                $order->update([
                    'status' => 'delivered',
                    'delivered_at' => now()
                ]);

                $updatedCount++;

                // Log the automatic update
                Log::info('Order automatically marked as delivered', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                    'shipped_at' => $order->shipped_at,
                    'auto_delivered_at' => now(),
                    'days_since_shipped' => $order->shipped_at->diffInDays(now())
                ]);

                $this->line("✓ Order #{$order->order_number} automatically marked as delivered");

            } catch (\Exception $e) {
                Log::error('Failed to automatically mark order as delivered', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage()
                ]);

                $this->error("✗ Failed to update Order #{$order->order_number}: {$e->getMessage()}");
            }
        }

        if ($updatedCount > 0) {
            $this->info("Successfully updated {$updatedCount} orders to delivered status.");
        } else {
            $this->info('No orders found that need automatic status update.');
        }

        $this->info('Automatic order status update completed.');

        return 0;
    }
}
