<?php

namespace App\Services;

use App\Models\CashbackPayment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Mock Payment Service for cashback functionality.
 * 
 * In a real application, this would integrate with payment providers
 * like Paystack, Flutterwave, or bank transfer APIs.
 * 
 * This mock implementation simulates real-world behavior with:
 * - Transaction IDs
 * - Processing delays (optional)
 * - Detailed logging
 * - Status tracking
 */
class PaymentService
{
    protected string $currency;
    protected string $currencySymbol;

    public function __construct()
    {
        $this->currency = config('achievements.cashback.currency', 'NGN');
        $this->currencySymbol = config('achievements.cashback.currency_symbol', '₦');
    }

    /**
     * Send cashback payment to a user.
     * 
     * @param User $user The recipient user
     * @param int $amount Amount in Naira
     * @param string $reason Reason for the cashback
     * @param array $context Additional metadata (e.g., badge info)
     * @return array Payment result with transaction details
     */
    public function sendCashback(User $user, int $amount, string $reason = 'Badge Unlock Reward', array $context = []): array
    {
        $transactionId = $this->generateTransactionId();
        $timestamp = now()->toIso8601String();

        // Log the payment attempt
        Log::info('💰 Cashback Payment Initiated', [
            'transaction_id' => $transactionId,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'amount' => $amount,
            'currency' => $this->currency,
            'reason' => $reason,
            'timestamp' => $timestamp,
        ]);

        // Simulate payment processing
        $result = $this->processPayment($user, $amount, $transactionId);

        // Persist cashback transaction for reporting.
        CashbackPayment::create([
            'user_id' => $user->id,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'currency' => $this->currency,
            'badge_key' => $context['badge_key'] ?? null,
            'badge_name' => $context['badge_name'] ?? null,
            'status' => $result['status'],
            'reason' => $reason,
            'processed_at' => now(),
        ]);

        // Log the result
        Log::info('💰 Cashback Payment Completed', [
            'transaction_id' => $transactionId,
            'status' => $result['status'],
            'message' => $result['message'],
        ]);

        return $result;
    }

    /**
     * Process the payment (mock implementation).
     */
    protected function processPayment(User $user, int $amount, string $transactionId): array
    {
        // Simulate successful payment (in real implementation, this would call payment API)
        // You could add random failures here for testing error handling
        $success = true; // rand(1, 100) <= 95; // 95% success rate simulation

        if ($success) {
            return [
                'success' => true,
                'status' => 'completed',
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'currency' => $this->currency,
                'formatted_amount' => "{$this->currencySymbol}{$amount}",
                'recipient' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                ],
                'message' => "{$this->currencySymbol}{$amount} cashback sent successfully to {$user->email}",
                'timestamp' => now()->toIso8601String(),
            ];
        }

        return [
            'success' => false,
            'status' => 'failed',
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'currency' => $this->currency,
            'error_code' => 'PAYMENT_FAILED',
            'message' => 'Payment processing failed. Will retry automatically.',
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Generate a unique transaction ID.
     */
    protected function generateTransactionId(): string
    {
        return 'TXN_' . strtoupper(Str::random(8)) . '_' . time();
    }

    /**
     * Get payment history for a user (mock implementation).
     */
    public function getPaymentHistory(User $user): array
    {
        $payments = $user->cashbackPayments()
            ->orderByDesc('processed_at')
            ->get(['transaction_id', 'amount', 'currency', 'status', 'badge_key', 'badge_name', 'reason', 'processed_at'])
            ->map(fn($payment) => [
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'formatted_amount' => "{$this->currencySymbol}{$payment->amount}",
                'status' => $payment->status,
                'badge_key' => $payment->badge_key,
                'badge_name' => $payment->badge_name,
                'reason' => $payment->reason,
                'processed_at' => optional($payment->processed_at)->toIso8601String(),
            ])
            ->toArray();

        return [
            'user_id' => $user->id,
            'total_cashback_earned' => (int) $user->cashbackPayments()
                ->where('status', 'completed')
                ->sum('amount'),
            'currency' => $this->currency,
            'payments' => $payments,
        ];
    }
}
