<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeGeneratedFiles extends Command
{
    protected $signature = 'storage:purge-generated {--dry-run : tampilkan yang akan dihapus tanpa eksekusi} {--older-than=2 : hapus file lebih tua dari N menit}';

    protected $description = 'Hapus file ephemeral hasil generate (storage/app/{private,public}/{invoices,exports,tmp}) + fallback file > N menit. Juga bersihkan payment.invoice_pdf yang menunjuk file yang sudah hilang.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $olderThan = max(0, (int) $this->option('older-than'));
        // Direktori ephemeral — hanya ini yang disapu.
        // JANGAN masukkan documents/payment-proofs/school-logos/avatars.
        $dirs = [
            ['private', 'invoices'],
            ['public', 'invoices'],
            ['private', 'exports'],
            ['public', 'exports'],
            ['private', 'tmp'],
            ['public', 'tmp'],
        ];
        $cutoff = now()->subMinutes($olderThan);
        $deleted = 0;
        $staleNulls = 0;

        foreach ($dirs as [$disk, $dir]) {
            $files = Storage::disk($disk)->files($dir);
            foreach ($files as $path) {
                // Fallback sweep: hanya file yang lastModified < cutoff
                try {
                    $mtime = Storage::disk($disk)->lastModified($path);
                    if ($mtime > $cutoff->getTimestamp()) {
                        continue;
                    }
                } catch (\Throwable $e) {
                    // jika tidak bisa baca mtime, tetap hapus (better boros dihapus)
                }
                if ($dry) {
                    $this->line("  [DRY-RUN] {$disk}:{$path}");
                } else {
                    Storage::disk($disk)->delete($path);
                    $deleted++;
                }
            }
        }

        // Kosongkan semua dokumen di storage sekarang (permintaan user) — HANYA jika tanpa older-than filter?
        // Tidak: flag --older-than sudah mengontrol. Untuk "kosongkan sekarang", user jalankan tanpa filter
        // atau dengan --older-than=0 agar semua ephemeral terhapus.

        // Bersihkan payment.invoice_pdf yang menunjuk file yang sudah tidak ada.
        $payments = \App\Models\Payment::whereNotNull('invoice_pdf')->get(['id', 'invoice_pdf']);
        foreach ($payments as $p) {
            $exists = false;
            foreach (['private', 'public'] as $disk) {
                if (Storage::disk($disk)->exists($p->invoice_pdf)) { $exists = true; break; }
            }
            // invoice di storage/app/public/invoices tapi disk private root-nya sama (app/private) —
            // jadi cek keduanya; jika tidak ada di keduanya, null-kan.
            if (!$exists) {
                if ($dry) {
                    $this->line("  [DRY-RUN] null invoice_pdf payment #{$p->id} ({$p->invoice_pdf})");
                } else {
                    \App\Models\Payment::whereKey($p->id)->update(['invoice_pdf' => null]);
                    $staleNulls++;
                }
            }
        }

        if ($dry) {
            $this->info('Dry-run selesai. Tidak ada file dihapus.');
        } else {
            $this->info("Selesai. File ephemeral terhapus: {$deleted}, payment.invoice_pdf di-null-kan: {$staleNulls}.");
        }

        return self::SUCCESS;
    }
}
