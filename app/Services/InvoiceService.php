<?php

namespace App\Services;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    /**
     * Nomor invoice milik sistem (bukan invoice Xendit).
     * Format: INV/YYYY/000001 dst. Idempotent — jika sudah ada, kembalikan apa adanya.
     */
    public function generateNumber(Payment $payment): string
    {
        if ($payment->invoice_number) {
            return $payment->invoice_number;
        }

        $year = now()->year;
        $prefix = 'INV/' . $year . '/';

        // urutan lanjutan dari nomor tertinggi yang sudah ada di tahun ini
        $last = Payment::where('invoice_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(invoice_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->value('invoice_number');

        $seq = 1;
        if ($last) {
            $seq = (int) substr($last, strlen($prefix)) + 1;
        } else {
            // fallback: belum ada invoice sama sekali → urut dari total data
            $seq = max(1, (int) Payment::count() + 1);
        }

        return $prefix . str_pad((string) $seq, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Terbitkan invoice: tetapkan nomor, render PDF, simpan, catat path di payment.
     * Mengembalikan path PDF (relatif storage/app/public).
     */
    public function issue(Payment $payment): string
    {
        if (!$payment->invoice_number) {
            $payment->update(['invoice_number' => $this->generateNumber($payment)]);
        }

        $pdf = Pdf::loadView('pdf.invoice', ['payment' => $payment]);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'INV-' . $payment->id . '.pdf';
        $path = 'invoices/' . $filename;
        \Illuminate\Support\Facades\Storage::disk('private')->put($path, $pdf->output());

        $payment->update([
            'invoice_pdf' => $path,
            'invoice_issued_at' => now(),
        ]);

        return $path;
    }

    /**
     * Angka → kata bahasa Indonesia (untuk baris "Terbilang").
     */
    public function terbilang($angka): string
    {
        $angka = (int) floor((float) $angka);
        if ($angka < 0) {
            return 'Nol';
        }

        $satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        $words = function ($n) use (&$words, $satuan) {
            if ($n < 12) return $satuan[$n];
            if ($n < 20) return $words($n - 10) . ' Belas';
            if ($n < 100) return $words(intdiv($n, 10)) . ' Puluh' . ($n % 10 ? ' ' . $words($n % 10) : '');
            if ($n < 200) return 'Seratus' . ($n % 100 ? ' ' . $words($n % 100) : '');
            if ($n < 1000) return $words(intdiv($n, 100)) . ' Ratus' . ($n % 100 ? ' ' . $words($n % 100) : '');
            if ($n < 2000) return 'Seribu' . ($n % 1000 ? ' ' . $words($n % 1000) : '');
            if ($n < 1000000) return $words(intdiv($n, 1000)) . ' Ribu' . ($n % 1000 ? ' ' . $words($n % 1000) : '');
            if ($n < 1000000000) return $words(intdiv($n, 1000000)) . ' Juta' . ($n % 1000000 ? ' ' . $words($n % 1000000) : '');
            if ($n < 1000000000000) return $words(intdiv($n, 1000000000)) . ' Miliar' . ($n % 1000000000 ? ' ' . $words($n % 1000000000) : '');
            return $words(intdiv($n, 1000000000000)) . ' Triliun' . ($n % 1000000000000 ? ' ' . $words($n % 1000000000000) : '');
        };

        return $words((int) $angka) ?: 'Nol';
    }
}
