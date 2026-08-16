<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            [
                'school_id' => 1,
                'name' => 'Teknik Komputer dan Jaringan',
                'code' => 'TKJ',
                'quota' => 72,
                'description' => 'Program keahlian yang mempelajari tentang instalasi, konfigurasi, dan pemeliharaan jaringan komputer',
                'requires_health_test' => true,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school_id' => 1,
                'name' => 'Rekayasa Perangkat Lunak',
                'code' => 'RPL',
                'quota' => 72,
                'description' => 'Program keahlian yang mempelajari tentang pengembangan dan pemrograman perangkat lunak',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school_id' => 1,
                'name' => 'Multimedia',
                'code' => 'MM',
                'quota' => 36,
                'description' => 'Program keahlian yang mempelajari tentang desain grafis, animasi, dan produksi multimedia',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => true,
            ],
            [
                'school_id' => 1,
                'name' => 'Teknik Elektronika Industri',
                'code' => 'TEI',
                'quota' => 36,
                'description' => 'Program keahlian yang mempelajari tentang sistem elektronika dan kontrol industri',
                'requires_health_test' => true,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
        ];

        foreach ($majors as $major) {
            $id = \DB::table('majors')->insertGetId(array_merge($major, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Kuota per jalur sesuai revisi.md
            $quotas = match ($major['code']) {
                'TKJ', 'RPL' => ['Reguler' => 40, 'Prestasi' => 20, 'Beasiswa' => 12],
                'MM', 'TEI' => ['Reguler' => 20, 'Prestasi' => 10, 'Beasiswa' => 6],
                default => ['Reguler' => 0, 'Prestasi' => 0, 'Beasiswa' => 0],
            };
            foreach ($quotas as $trackName => $q) {
                $trackId = \DB::table('registration_tracks')->where('name', $trackName)->value('id');
                if ($trackId) {
                    \DB::table('major_track_quotas')->updateOrInsert(
                        ['major_id' => $id, 'registration_track_id' => $trackId],
                        ['quota' => $q, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }
        }
    }
}
