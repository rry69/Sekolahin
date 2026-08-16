<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'TK Negeri 1 Jakarta',
                'address' => 'Jl. Pegangsaan Timur No. 2, Jakarta Pusat 10320',
                'phone' => '021-3901234',
                'email' => 'info@tkn1jakarta.sch.id',
                'principal_name' => 'Hj. Nurhayati, S.Pd',
                'level' => 'TK',
            ],
            [
                'name' => 'SD Negeri 1 Jakarta',
                'address' => 'Jl. Kenari No. 2, Jakarta Pusat 10430',
                'phone' => '021-3145678',
                'email' => 'info@sdn1jakarta.sch.id',
                'principal_name' => 'Ahmad Fauzi, S.Pd',
                'level' => 'SD',
            ],
            [
                'name' => 'SMP Negeri 1 Jakarta',
                'address' => 'Jl. Pejaten Barat No. 1, Jakarta Selatan 12510',
                'phone' => '021-7801234',
                'email' => 'info@smpn1jakarta.sch.id',
                'principal_name' => 'Drs. Bambang Wijaya, M.Pd',
                'level' => 'SMP',
            ],
            [
                'name' => 'SMK Negeri 1 Jakarta',
                'address' => 'Jl. Budi Utomo No.7, Jakarta Pusat 10710',
                'phone' => '021-3456789',
                'email' => 'info@smkn1jakarta.sch.id',
                'principal_name' => 'Dr. Budi Santoso, M.Pd',
                'level' => 'SMK',
            ],
            [
                'name' => 'SMA Negeri 1 Jakarta',
                'address' => 'Jl. Budi Utomo No.9, Jakarta Pusat 10710',
                'phone' => '021-3456790',
                'email' => 'info@sman1jakarta.sch.id',
                'principal_name' => 'Dra. Siti Rahmawati, M.Pd',
                'level' => 'SMA',
            ],
            [
                'name' => 'SMA Negeri 8 Jakarta',
                'address' => 'Jl. Taman Bukit Duri No. 1, Jakarta Selatan 12840',
                'phone' => '021-8295510',
                'email' => 'info@sman8jakarta.sch.id',
                'principal_name' => 'Drs. Agus Salim, M.M',
                'level' => 'SMA',
            ],
        ];

        foreach ($schools as $school) {
            $level = $school['level'];
            unset($school['level']);

            $levelId = \DB::table('school_levels')->where('name', $level)->value('id');
            if (! $levelId) {
                continue;
            }

            \DB::table('schools')->updateOrInsert(
                ['name' => $school['name']],
                array_merge($school, ['updated_at' => now(), 'created_at' => now()])
            );

            $schoolId = \DB::table('schools')->where('name', $school['name'])->value('id');

            \DB::table('school_level_school')->updateOrInsert(
                ['school_id' => $schoolId, 'school_level_id' => $levelId],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
