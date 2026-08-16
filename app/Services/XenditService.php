<?php

namespace App\Services;

use App\Models\Payment;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Exception;
use Illuminate\Support\Facades\Request as RequestFacade;

class XenditService
{
    protected $invoiceApi;

    public function __construct()
    {
        Configuration::setXenditKey(config('services.xendit.api_key'));
        $this->invoiceApi = new InvoiceApi();
    }

    public function createInvoice(Payment $payment)
    {
        try {
            $registration = $payment->registration;
            $applicant = $registration->applicant;
            
            $externalId = 'PAYMENT-' . $payment->id . '-' . time();
            
            $createInvoiceRequest = new CreateInvoiceRequest([
                'external_id' => $externalId,
                'amount' => (float) $payment->amount,
                'payer_email' => $applicant->user->email,
                'description' => $this->getPaymentDescription($payment),
                'invoice_duration' => 86400,
                'success_redirect_url' => route('registration.show', $registration),
                'failure_redirect_url' => route('registration.show', $registration),
                'currency' => 'IDR',
                'items' => [
                    [
                        'name' => $this->getPaymentDescription($payment),
                        'quantity' => 1,
                        'price' => (float) $payment->amount,
                        'category' => 'education'
                    ]
                ],
                'customer' => [
                    'given_names' => $applicant->full_name,
                    'email' => $applicant->user->email,
                    'mobile_number' => $applicant->phone ?? '',
                ],
                'customer_notification_preference' => [
                    'invoice_created' => ['email', 'whatsapp'],
                    'invoice_reminder' => ['email', 'whatsapp'],
                    'invoice_paid' => ['email', 'whatsapp']
                ]
            ]);

            $result = $this->invoiceApi->createInvoice($createInvoiceRequest);

            $id = self::invoiceField($result, 'id');
            $url = self::invoiceField($result, 'invoice_url');

            $payment->update([
                'xendit_invoice_id' => $id,
                'xendit_invoice_url' => $url,
                'external_id' => $externalId,
                'status' => 'pending'
            ]);

            return [
                'success' => true,
                'invoice_id' => $id,
                'invoice_url' => $url,
                'external_id' => $externalId
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getInvoice($invoiceId)
    {
        try {
            $result = $this->invoiceApi->getInvoiceById($invoiceId);
            return [
                'success' => true,
                'invoice' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function handleWebhookCallback($payload)
    {
        try {
            $externalId = $payload['external_id'] ?? null;
            $status = isset($payload['status']) ? strtoupper((string) $payload['status']) : null;
            $paidAt = $payload['paid_at'] ?? null;
            $paymentMethod = $payload['payment_method'] ?? null;
            $invoiceId = $payload['id'] ?? null;

            if (!$externalId || !$status) {
                return ['success' => false, 'error' => 'Invalid payload'];
            }

            $payment = Payment::where('external_id', $externalId)->first();

            if (!$payment) {
                return ['success' => false, 'error' => 'Payment not found'];
            }

            if ($payment->status === 'verified' && ($status === 'PAID' || $status === 'SETTLED')) {
                return ['success' => true, 'message' => 'Payment already verified'];
            }

            if ($status === 'PAID' || $status === 'SETTLED') {
                $friendlyMethod = $paymentMethod ? self::friendlyXenditMethod($paymentMethod) : 'Xendit';

                $registration = $payment->registration;
                if ($registration->payment_status === 'paid' && $registration->payments()->where('status', 'verified')->exists()) {
                    return ['success' => true, 'message' => 'Already paid'];
                }

                $payment->update([
                    'status' => 'verified',
                    'xendit_paid_at' => $paidAt ? now()->parse($paidAt) : now(),
                    'xendit_payment_method' => $paymentMethod,
                    'verified_at' => now(),
                    'notes' => 'Pembayaran berhasil melalui ' . $friendlyMethod . ' via Xendit',
                ]);

                $registration->update(['payment_status' => 'paid']);
                $registration->payments()
                    ->where('id', '!=', $payment->id)
                    ->whereIn('status', ['pending', 'rejected'])
                    ->where('payment_method', 'online')
                    ->whereNull('xendit_paid_at')
                    ->whereNull('proof_file')
                    ->delete();

                $registration->loadMissing('registrationTrack', 'major');
                $regNumber = $registration->registration_number;

                ActivityLogger::log('payment.webhook_paid', 'Webhook Xendit PAID (' . $friendlyMethod . '): ' . $regNumber, $payment, [
                    'registration_number' => $regNumber,
                    'external_id' => $externalId,
                    'xendit_invoice_id' => $invoiceId,
                    'xendit_payment_method' => $paymentMethod,
                    'friendly_method' => $friendlyMethod,
                    'ip' => RequestFacade::ip(),
                ]);

                ActivityLogger::statusChange('registration.payment_status', 'Pembayaran LUNAS via ' . $friendlyMethod . ' (Xendit): ' . $regNumber, $registration, $registration->getOriginal('payment_status') ?? 'pending', 'paid', [
                    'registration_number' => $regNumber,
                    'xendit_payment_method' => $paymentMethod,
                    'friendly_method' => $friendlyMethod,
                    'external_id' => $externalId,
                ]);

                $registration->refresh();
                $enroller = new class { use \App\Traits\EnrollsStudent { enrollIfReady as public enrollRegistration; } };
                $enroller->enrollRegistration($registration);

                return ['success' => true, 'message' => 'Payment verified'];
            }

            if ($status === 'EXPIRED') {
                $payment->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'Invoice expired',
                    'verified_at' => now()
                ]);

                $payment->registration->update(['payment_status' => 'failed']);

                ActivityLogger::log('payment.webhook_expired', 'Webhook Xendit EXPIRED: ' . $payment->registration->registration_number, $payment, [
                    'registration_number' => $payment->registration->registration_number,
                    'external_id' => $externalId,
                    'ip' => RequestFacade::ip(),
                ]);

                return ['success' => true, 'message' => 'Payment expired'];
            }

            return ['success' => true, 'message' => 'Status updated'];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function getPaymentDescription(Payment $payment)
    {
        if ($payment->payment_type === 'registration_fee') {
            return 'Biaya Pendaftaran - ' . $payment->registration->registration_number;
        }
        return 'Biaya Daftar Ulang - ' . $payment->registration->registration_number;
    }

    public static function friendlyXenditMethod(?string $raw): string
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return 'Xendit';
        }

        $map = [
            'CREDIT_CARD' => 'Kartu Kredit',
            'VIRTUAL_ACCOUNT' => 'Virtual Account',
            'RETAIL_OUTLET' => 'Retail Outlet',
            'EWALLET' => 'E-Wallet',
            'QR_CODE' => 'QRIS',
            'QRIS' => 'QRIS',
            'DD_BRI' => 'Debit BRI',
            'DD_BCA_KLIKPAY' => 'BCA KlikPay',
            'DD_MANDIRI' => 'Mandiri Debit',
        ];

        $upper = strtoupper($raw);

        if (isset($map[$upper])) {
            return $map[$upper];
        }

        foreach ($map as $key => $label) {
            if (str_contains($upper, $key)) {
                // Contoh: BCA_VIRTUAL_ACCOUNT → Virtual Account (BCA)
                if ($key === 'VIRTUAL_ACCOUNT' || $key === 'RETAIL_OUTLET' || $key === 'EWALLET') {
                    $bank = str_replace(['_' . $key, $key . '_', '_'], [' ', ' ', ' '], $upper);
                    $bank = trim($bank);
                    if ($bank !== '' && $bank !== $upper) {
                        return $label . ' (' . ucwords(strtolower($bank)) . ')';
                    }
                }

                return $label;
            }
        }

        return ucwords(strtolower(str_replace('_', ' ', $raw))) . ' via Xendit';
    }

    public static function invoiceField($invoice, string $key): mixed
    {
        if (is_array($invoice) && array_key_exists($key, $invoice)) {
            return $invoice[$key];
        }
        if (is_object($invoice)) {
            $getter = 'get' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($invoice, $getter)) {
                $val = $invoice->$getter();
                if ($val instanceof \Stringable || (is_object($val) && method_exists($val, 'getValue'))) {
                    return $val->getValue();
                }
                return $val;
            }
            if (isset($invoice->$key)) {
                return $invoice->$key;
            }
            if ($invoice instanceof \ArrayAccess && isset($invoice[$key])) {
                $raw = $invoice[$key];
                if ($raw instanceof \Stringable || (is_object($raw) && method_exists($raw, 'getValue'))) {
                    return $raw->getValue();
                }
                return $raw;
            }
        }
        return null;
    }

    public static function invoiceStatusValue($invoice): ?string
    {
        $raw = self::invoiceField($invoice, 'status');
        if ($raw === null) return null;
        if (is_string($raw)) return strtoupper($raw);
        if ($raw instanceof \Stringable) return strtoupper((string) $raw);
        if (is_object($raw) && method_exists($raw, 'getValue')) return strtoupper((string) $raw->getValue());
        return strtoupper((string) $raw);
    }

    public static function invoiceMethodValue($invoice): ?string
    {
        $raw = self::invoiceField($invoice, 'payment_method');
        if ($raw === null) {
            $raw = self::invoiceField($invoice, 'payment_channel');
        }
        if ($raw === null) return null;
        if (is_string($raw)) return $raw;
        if ($raw instanceof \Stringable) return (string) $raw;
        if (is_object($raw) && method_exists($raw, 'getValue')) return (string) $raw->getValue();
        return (string) $raw;
    }

    public function verifyCallbackToken($callbackToken)
    {
        $configToken = config('services.xendit.webhook_token');
        
        if (empty($configToken)) {
            return true;
        }

        return $callbackToken === $configToken;
    }
}
