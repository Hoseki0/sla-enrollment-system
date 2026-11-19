<?php

namespace App\Services;

use App\Models\QuarterlyPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    public function isDemoMode(): bool
    {
        return config('services.paymongo.demo_mode', true);
    }

    /**
     * Initiate a payment checkout session.
     * In demo mode, returns a local checkout URL.
     * In production, creates a PayMongo checkout session for GCash/Maya.
     */
    public function initiateCheckout(QuarterlyPayment $payment, string $method): array
    {
        if ($this->isDemoMode()) {
            return [
                'success'      => true,
                'checkout_url' => route('student.payments.checkout', [
                    'payment' => $payment->payment_ref,
                    'method'  => $method,
                ]),
                'session_id'   => 'demo_' . $payment->payment_ref,
            ];
        }

        return $this->createPayMongoCheckout($payment, $method);
    }

    /**
     * Confirm payment after gateway callback or demo completion.
     */
    public function confirmPayment(QuarterlyPayment $payment, ?string $gatewayRef = null): bool
    {
        $payment->update([
            'status'      => 'paid',
            'gateway_ref' => $gatewayRef ?? ('DEMO-' . strtoupper(substr(uniqid(), -8))),
            'paid_at'     => now(),
        ]);

        return true;
    }

    /**
     * Mark payment as failed.
     */
    public function failPayment(QuarterlyPayment $payment, ?string $reason = null): void
    {
        $payment->update([
            'status'  => 'failed',
            'remarks' => $reason,
        ]);
    }

    /**
     * Create PayMongo checkout session (production).
     */
    protected function createPayMongoCheckout(QuarterlyPayment $payment, string $method): array
    {
        $secretKey = config('services.paymongo.secret_key');
        $baseUrl   = config('services.paymongo.base_url', 'https://api.paymongo.com/v1');

        $paymentMethodTypes = match ($method) {
            'gcash' => ['gcash'],
            'maya'  => ['paymaya'],
            default => ['gcash', 'paymaya'],
        };

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->post("{$baseUrl}/checkout_sessions", [
                    'data' => [
                        'attributes' => [
                            'send_email_receipt' => true,
                            'show_description'   => true,
                            'show_line_items'    => true,
                            'line_items' => [[
                                'currency' => 'PHP',
                                'amount'   => (int) ($payment->amount * 100),
                                'name'     => "Quarter {$payment->quarter} Tuition Fee",
                                'quantity' => 1,
                            ]],
                            'payment_method_types' => $paymentMethodTypes,
                            'success_url' => route('student.payments.callback', ['payment' => $payment->payment_ref, 'status' => 'success']),
                            'cancel_url'  => route('student.payments.callback', ['payment' => $payment->payment_ref, 'status' => 'cancelled']),
                            'description' => "SLA Enrollment - Q{$payment->quarter} Payment ({$payment->payment_ref})",
                            'metadata'    => [
                                'payment_ref' => $payment->payment_ref,
                                'student_id'  => (string) $payment->student_id,
                                'quarter'     => (string) $payment->quarter,
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json('data');
                $payment->update(['gateway_ref' => $data['id'] ?? null]);

                return [
                    'success'      => true,
                    'checkout_url' => $data['attributes']['checkout_url'] ?? null,
                    'session_id'   => $data['id'] ?? null,
                ];
            }

            Log::error('PayMongo checkout failed', ['response' => $response->json()]);

            return ['success' => false, 'message' => 'Payment gateway error. Please try again.'];
        } catch (\Exception $e) {
            Log::error('PayMongo exception', ['message' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Unable to connect to payment gateway.'];
        }
    }
}
