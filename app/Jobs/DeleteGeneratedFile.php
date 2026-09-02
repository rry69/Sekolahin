<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DeleteGeneratedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $disk,
        public string $path,
        public ?int $paymentId = null,
    ) {}

    public function handle(): void
    {
        if (Storage::disk($this->disk)->exists($this->path)) {
            Storage::disk($this->disk)->delete($this->path);
        }
        // Jika ini invoice terikat payment, null-kan kolom supaya tidak 404 stale.
        if ($this->paymentId) {
            try {
                \App\Models\Payment::whereKey($this->paymentId)
                    ->where('invoice_pdf', $this->path)
                    ->update(['invoice_pdf' => null]);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
