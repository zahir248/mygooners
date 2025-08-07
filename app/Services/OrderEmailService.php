<?php

namespace App\Services;

use App\Models\Order;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderEmailService
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Send order confirmation emails after successful payment
     */
    public function sendOrderConfirmationEmails(Order $order)
    {
        try {
            // Generate invoice
            $invoicePath = $this->invoiceService->generateInvoice($order);

            // Send email to shipping address
            $this->sendEmailToAddress($order, $order->shipping_email, $order->shipping_name, $invoicePath);

            // Send email to billing address if different from shipping
            if ($order->billing_email !== $order->shipping_email) {
                $this->sendEmailToAddress($order, $order->billing_email, $order->billing_name, $invoicePath);
            }

            // Clean up invoice file after sending emails
            if ($invoicePath) {
                $this->invoiceService->deleteInvoice($invoicePath);
            }

            Log::info('Order confirmation emails sent successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'shipping_email' => $order->shipping_email,
                'billing_email' => $order->billing_email,
                'emails_sent' => $order->billing_email !== $order->shipping_email ? 2 : 1
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation emails', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send email to a specific address
     */
    private function sendEmailToAddress(Order $order, $email, $name, $invoicePath = null)
    {
        try {
            Mail::to($email)->send(new OrderConfirmationMail($order, $invoicePath));

            Log::info('Order confirmation email sent', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'email' => $email,
                'name' => $name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send email to address', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Send order status update email
     */
    public function sendOrderStatusUpdateEmail(Order $order, $status, $message = null)
    {
        try {
            // This can be expanded later for status update emails
            Log::info('Order status update email would be sent', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status,
                'message' => $message
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send order status update email', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
} 