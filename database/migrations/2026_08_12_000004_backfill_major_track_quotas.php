<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('major_track_quotas') || ! Schema::hasTable('majors')) {
            return;
        }

        $quotasByCode = [
            'TKJ' => ['Reguler' => 40, 'Prestasi' => 20, 'Beasiswa' => 12],
            'RPL' => ['Reguler' => 40, 'Prestasi' => 20, 'Beasiswa' => 12],
            'MM'  => ['Reguler' => 20, 'Prestasi' => 10, 'Beasiswa' => 6],
            'TEI' => ['Reguler' => 20, 'Prestasi' => 10, 'Beasiswa' => 6],
        ];

        $tracks = DB::table('registration_tracks')->pluck('id', 'name');

        foreach (DB::table('majors')->get() as $major) {
            $map = $quotasByCode[$major->code] ?? null;
            if (! $map) {
                // fallback: bagi quota lama 72/36 secara proporsional revisi.md
                $total = (int) ($major->quota ?? 0);
                if ($total === 72) $map = $quotasByCode['TKJ'];
                elseif ($total === 36) $map = $quotasByCode['MM'];
                else continue;
            }
            foreach ($map as $trackName => $q) {
                $trackId = $tracks[$trackName] ?? null;
                if (! $trackId) continue;
                DB::table('major_track_quotas')->updateOrInsert(
                    ['major_id' => $major->id, 'registration_track_id' => $trackId],
                    ['quota' => $q, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        // keep data, no truncate
    }
};
