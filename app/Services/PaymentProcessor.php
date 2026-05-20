<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Invoice;

class PaymentProcessor
{
    public static function process(Order $order, string $method, float $amount, string $type = 'full', ?string $transactionId = null): Payment
    {
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        $balanceDue = $order->total - $totalPaid - $amount;

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->buyer_id,
            'type' => $type,
            'amount' => $amount,
            'balance_due' => max(0, $balanceDue),
            'method' => $method,
            'status' => 'completed',
            'transaction_id' => $transactionId ?? self::generateTransactionId(),
            'paid_at' => now(),
        ]);

        if ($balanceDue <= 0) {
            $order->payment_status = 'paid';
            $order->save();

            OrderStateMachine::transition($order, Order::STATUS_PAID);
        } else {
            $order->payment_status = 'partial';
            $order->save();
        }

        self::generateInvoice($order);

        return $payment;
    }

    public static function refund(Payment $payment, float $amount, ?string $reason = null): Payment
    {
        $refund = Payment::create([
            'order_id' => $payment->order_id,
            'user_id' => $payment->user_id,
            'type' => 'refund',
            'amount' => -$amount,
            'balance_due' => 0,
            'method' => $payment->method,
            'status' => 'completed',
            'transaction_id' => self::generateTransactionId(),
            'metadata' => ['reason' => $reason, 'original_payment_id' => $payment->id],
            'paid_at' => now(),
        ]);

        $order = $payment->order;
        $order->payment_status = 'refunded';
        $order->save();

        OrderStateMachine::transition($order, Order::STATUS_REFUNDED);

        return $refund;
    }

    public static function generateInvoice(Order $order): Invoice
    {
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        $balanceDue = $order->total - $totalPaid;

        return Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => self::generateInvoiceNumber(),
            'user_id' => $order->buyer_id,
            'amount_due' => $balanceDue,
            'amount_paid' => $totalPaid,
            'due_date' => now()->addDays(30),
            'status' => $balanceDue <= 0 ? 'paid' : 'unpaid',
        ]);
    }

    private static function generateTransactionId(): string
    {
        return 'TXN-' . strtoupper(uniqid());
    }

    private static function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Y') . '-' . str_pad((Invoice::count() + 1), 6, '0', STR_PAD_LEFT);
    }
}
