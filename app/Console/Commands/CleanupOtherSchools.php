<?php

namespace App\Console\Commands;

use App\Models\Major;
use App\Models\Registration;
use App\Models\School;
use Illuminate\Console\Command;

class CleanupOtherSchools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:cleanup {--school-id= : ID sekolah yang dipertahankan (default: sekolah pertama)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus permanen semua sekolah lain beserta data pendaftaran, jurusan, dan tes terkait, sehingga hanya tersisa 1 sekolah.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $keepId = (int) $this->option('school-id');

        if ($keepId <= 0) {
            $primary = School::orderBy('id')->first();
            $keepId = $primary?->id;
        }

        if (!$keepId) {
            $this->error('Tidak ada sekolah ditemukan.');

            return self::FAILURE;
        }

        if (!School::whereKey($keepId)->exists()) {
            $this->error("Sekolah dengan ID {$keepId} tidak ditemukan.");

            return self::FAILURE;
        }

        $schoolsToDelete = School::whereKeyNot($keepId)->get();

        if ($schoolsToDelete->isEmpty()) {
            $this->info('Tidak ada sekolah lain yang perlu dihapus.');

            return self::SUCCESS;
        }

        $ids = $schoolsToDelete->pluck('id');
        $this->warn("Menghapus " . $schoolsToDelete->count() . " sekolah: " . $schoolsToDelete->pluck('name')->implode(', '));

        // Registrasi sekolah lain (cascade ke payments, dokumen, re-registrasi).
        $deletedRegistrations = Registration::whereIn('school_id', $ids)->delete();
        $this->info("Registrasi terhapus: {$deletedRegistrations}");

        // Jurusan sekolah lain (cascade delete relasi).
        $deletedMajors = Major::whereIn('school_id', $ids)->delete();
        $this->info("Jurusan terhapus: {$deletedMajors}");

        // Pivot jenjang.
        \DB::table('school_level_school')->whereIn('school_id', $ids)->delete();

        // Sekolah itu sendiri.
        $deletedSchools = School::whereIn('id', $ids)->delete();
        $this->info("Sekolah terhapus: {$deletedSchools}");

        $remaining = School::count();
        $this->info("Sekolah tersisa: {$remaining}");

        return self::SUCCESS;
    }
}
