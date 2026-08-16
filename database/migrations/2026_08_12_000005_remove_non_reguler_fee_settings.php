<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hapus setting fee_* untuk jalur non-Reguler (revisi.md):
     * biaya Prestasi/Beasiswa diinput admin manual setelah verifikasi berkas,
     * bukan dari setting.
     */
    public function up(): void
    {
        $regulerId = DB::table('registration_tracks')->whereRaw('LOWER(name) = ?', ['reguler'])->value('id');
        if (! $regulerId) {
            return;
        }

        $toDelete = DB::table('settings')
            ->where('key', 'like', 'fee_%')
            ->get()
            ->filter(function ($s) use ($regulerId) {
                $trackId = (int) substr((string) $s->key, strrpos((string) $s->key, '_') + 1);
                return $trackId !== (int) $regulerId;
            })
            ->pluck('id');

        if ($toDelete->isNotEmpty()) {
            DB::table('settings')->whereIn('id', $toDelete)->delete();
        }
    }

    public function down(): void
    {
        // tidak mengembalikan data yang dihapus
    }
};
