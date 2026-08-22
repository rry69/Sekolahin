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
        $schools = \DB::table('schools')->pluck('id', 'name');

        $majors = [
            // ===== SMK Negeri 1 Jakarta =====
            [
                'school' => 'SMK Negeri 1 Jakarta',
                'level' => 'SMK',
                'name' => 'Teknik Komputer dan Jaringan',
                'code' => 'TKJ',
                'quota' => 72,
                'description' => 'Program keahlian yang mempelajari tentang instalasi, konfigurasi, dan pemeliharaan jaringan komputer',
                'requires_health_test' => true,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMK Negeri 1 Jakarta',
                'level' => 'SMK',
                'name' => 'Rekayasa Perangkat Lunak',
                'code' => 'RPL',
                'quota' => 72,
                'description' => 'Program keahlian yang mempelajari tentang pengembangan dan pemrograman perangkat lunak',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMK Negeri 1 Jakarta',
                'level' => 'SMK',
                'name' => 'Multimedia',
                'code' => 'MM',
                'quota' => 36,
                'description' => 'Program keahlian yang mempelajari tentang desain grafis, animasi, dan produksi multimedia',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => true,
            ],
            [
                'school' => 'SMK Negeri 1 Jakarta',
                'level' => 'SMK',
                'name' => 'Teknik Elektronika Industri',
                'code' => 'TEI',
                'quota' => 36,
                'description' => 'Program keahlian yang mempelajari tentang sistem elektronika dan kontrol industri',
                'requires_health_test' => true,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],

            // ===== SMA Negeri 1 Jakarta (Kurikulum Merdeka) =====
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Matematika dan Ilmu Pengetahuan Alam',
                'code' => 'MIPA',
                'quota' => 72,
                'description' => 'Kelompok mata pelajaran pilihan Matematika dan Ilmu Pengetahuan Alam (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Ilmu Pengetahuan Sosial',
                'code' => 'IPS',
                'quota' => 72,
                'description' => 'Kelompok mata pelajaran pilihan Ilmu Pengetahuan Sosial (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Bahasa dan Budaya',
                'code' => 'BHS',
                'quota' => 36,
                'description' => 'Kelompok mata pelajaran pilihan Bahasa dan Budaya (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Peminatan Fisika',
                'code' => 'FIS',
                'quota' => 36,
                'description' => 'Mata pelajaran pilihan lanjutan Fisika (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Peminatan Kimia',
                'code' => 'KIM',
                'quota' => 36,
                'description' => 'Mata pelajaran pilihan lanjutan Kimia (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Peminatan Biologi',
                'code' => 'BIO',
                'quota' => 36,
                'description' => 'Mata pelajaran pilihan lanjutan Biologi (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Peminatan Ekonomi',
                'code' => 'EKO',
                'quota' => 36,
                'description' => 'Mata pelajaran pilihan lanjutan Ekonomi (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Peminatan Sosiologi',
                'code' => 'SOS',
                'quota' => 36,
                'description' => 'Mata pelajaran pilihan lanjutan Sosiologi (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Peminatan Bahasa Jepang',
                'code' => 'BJP',
                'quota' => 36,
                'description' => 'Mata pelajaran pilihan bahasa asing Jepang (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Peminatan Bahasa Inggris',
                'code' => 'BIG',
                'quota' => 36,
                'description' => 'Mata pelajaran pilihan bahasa asing Inggris lanjutan (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 1 Jakarta',
                'level' => 'SMA',
                'name' => 'Peminatan Informatika',
                'code' => 'INF',
                'quota' => 36,
                'description' => 'Mata pelajaran pilihan Informatika dan pengembangan digital (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],

            // ===== SMA Negeri 8 Jakarta (Kurikulum Merdeka) =====
            [
                'school' => 'SMA Negeri 8 Jakarta',
                'level' => 'SMA',
                'name' => 'Matematika dan Ilmu Pengetahuan Alam',
                'code' => 'MIPA',
                'quota' => 72,
                'description' => 'Kelompok mata pelajaran pilihan Matematika dan Ilmu Pengetahuan Alam (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 8 Jakarta',
                'level' => 'SMA',
                'name' => 'Ilmu Pengetahuan Sosial',
                'code' => 'IPS',
                'quota' => 72,
                'description' => 'Kelompok mata pelajaran pilihan Ilmu Pengetahuan Sosial (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
            [
                'school' => 'SMA Negeri 8 Jakarta',
                'level' => 'SMA',
                'name' => 'Peminatan Biologi',
                'code' => 'BIO',
                'quota' => 36,
                'description' => 'Mata pelajaran pilihan lanjutan Biologi (Kurikulum Merdeka)',
                'requires_health_test' => false,
                'requires_interview' => false,
                'requires_skill_test' => false,
            ],
        ];

        foreach ($majors as $major) {
            $levelId = \DB::table('school_levels')->where('name', $major['level'])->value('id');
            $schoolId = $schools[$major['school']] ?? null;
            unset($major['school'], $major['level']);
            // Kolom tes dihapus dari schema (SPMB tidak menggunakan ujian tes).
            unset($major['requires_health_test'], $major['requires_interview'], $major['requires_skill_test']);

            if (! $schoolId || ! $levelId) {
                continue;
            }

            \DB::table('majors')->updateOrInsert(
                ['school_id' => $schoolId, 'name' => $major['name']],
                array_merge($major, [
                    'school_id' => $schoolId,
                    'school_level_id' => $levelId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );

            $majorId = \DB::table('majors')->where('school_id', $schoolId)
                ->where('name', $major['name'])->value('id');

            // Kuota per jalur sesuai revisi.md
            $quotas = match ($major['code']) {
                'TKJ', 'RPL', 'MIPA', 'IPS' => ['Reguler' => 40, 'Prestasi' => 20, 'Beasiswa' => 12],
                'MM', 'TEI', 'BHS' => ['Reguler' => 20, 'Prestasi' => 10, 'Beasiswa' => 6],
                default => ['Reguler' => 20, 'Prestasi' => 10, 'Beasiswa' => 6],
            };
            foreach ($quotas as $trackName => $q) {
                $trackId = \DB::table('registration_tracks')->where('name', $trackName)->value('id');
                if ($trackId) {
                    \DB::table('major_track_quotas')->updateOrInsert(
                        ['major_id' => $majorId, 'registration_track_id' => $trackId],
                        ['quota' => $q, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }
        }
    }
}
