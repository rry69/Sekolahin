<?php

namespace App\Traits;

use App\Models\Registration;
use App\Services\ActivityLogger;
use App\Services\XenditService;

trait SyncsXenditPayment
{
    protected function syncXenditPayment(Registration $registration): void
    {
        if ($registration->payment_status === 'paid') return;
        $payment = $registration->payments()->where('payment_method','online')->where('status','pending')->whereNotNull('xendit_invoice_id')->latest()->first();
        if (!$payment) return;
        try { $result = $this->xenditService->getInvoice($payment->xendit_invoice_id); } catch (\Throwable $e) { return; }
        if (empty($result['success'])) return;
        $status = XenditService::invoiceStatusValue($result['invoice']);
        $raw = XenditService::invoiceMethodValue($result['invoice']);
        $friendly = XenditService::friendlyXenditMethod($raw);
        if ($status === 'PAID' || $status === 'SETTLED') {
            $payment->update(['status'=>'verified','xendit_paid_at'=>now(),'xendit_payment_method'=>$raw,'verified_at'=>now(),'notes'=>'Pembayaran berhasil melalui '.$friendly.' via Xendit']);
            $registration->update(['payment_status'=>'paid']);
            ActivityLogger::statusChange('registration.payment_status','Pembayaran LUNAS via '.$friendly.' (Xendit, sinkronisasi): '.$registration->registration_number,$registration,$registration->getOriginal('payment_status')??'pending','paid',['registration_number'=>$registration->registration_number,'xendit_payment_method'=>$raw,'friendly_method'=>$friendly]);
            $registration->refresh(); $this->enrollIfReady($registration);
        } elseif ($status === 'EXPIRED') {
            $payment->update(['status'=>'rejected','rejection_reason'=>'Invoice expired','verified_at'=>now()]);
            $registration->update(['payment_status'=>'failed']);
        }
    }
}
