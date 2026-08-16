<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('majors') || ! Schema::hasTable('school_level_school')) {
            return;
        }

        Schema::table('majors', function (Blueprint $table) {
            $table->unsignedBigInteger('school_level_id')->nullable()->after('school_id');
            $table->foreign('school_level_id')->references('id')->on('school_levels')->nullOnDelete();
        });

        // Backfill school_level_id dari pivot (level pertama per sekolah).
        $pivots = DB::table('school_level_school')
            ->orderBy('school_id')
            ->orderBy('school_level_id')
            ->get();

        $levelBySchool = [];
        foreach ($pivots as $pivot) {
            if (! isset($levelBySchool[$pivot->school_id])) {
                $levelBySchool[$pivot->school_id] = $pivot->school_level_id;
            }
        }

        foreach (DB::table('majors')->get() as $major) {
            $levelId = $levelBySchool[$major->school_id] ?? null;
            if (! $levelId) {
                continue;
            }
            DB::table('majors')->where('id', $major->id)->update(['school_level_id' => $levelId]);
        }

        // Normalisasi data:
        // 1. Sekolah SMK Negeri 1 Jakarta hanya melayani jenjang SMK (id 5).
        $smkSchool = DB::table('schools')->where('name', 'SMK Negeri 1 Jakarta')->value('id');
        $smkLevel  = DB::table('school_levels')->where('name', 'SMK')->value('id');
        if ($smkSchool && $smkLevel) {
            DB::table('school_level_school')->where('school_id', $smkSchool)->where('school_level_id', '!=', $smkLevel)->delete();
        }

        // 2. Tambah sekolah SMA Negeri 1 Jakarta yang melayani jenjang SMA (id 4).
        $smaLevel = DB::table('school_levels')->where('name', 'SMA')->value('id');
        if ($smaLevel && ! DB::table('schools')->where('name', 'SMA Negeri 1 Jakarta')->exists()) {
            $smaSchoolId = DB::table('schools')->insertGetId([
                'name'           => 'SMA Negeri 1 Jakarta',
                'address'        => 'Jl. Budi Utomo No. 9, Jakarta Pusat 10710',
                'phone'          => '021-3456790',
                'email'          => 'info@sman1jakarta.sch.id',
                'principal_name' => 'Dra. Siti Rahmawati, M.Pd',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            DB::table('school_level_school')->insert([
                'school_id'       => $smaSchoolId,
                'school_level_id' => $smaLevel,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 3. Tambah jurusan SMA dengan school_level_id jenjang SMA.
            $tracks = DB::table('registration_tracks')->pluck('id', 'name');
            $smaMajors = [
                ['name' => 'Matematika dan Ilmu Pengetahuan Alam', 'code' => 'MIPA', 'quota' => 72, 'description' => 'Peminatan Matematika dan Ilmu Pengetahuan Alam'],
                ['name' => 'Ilmu Pengetahuan Sosial', 'code' => 'IPS', 'quota' => 72, 'description' => 'Peminatan Ilmu Pengetahuan Sosial'],
                ['name' => 'Bahasa dan Budaya', 'code' => 'BHS', 'quota' => 36, 'description' => 'Peminatan Bahasa dan Budaya'],
            ];

            foreach ($smaMajors as $smaMajor) {
                $id = DB::table('majors')->insertGetId([
                    'school_id'            => $smaSchoolId,
                    'school_level_id'      => $smaLevel,
                    'name'                 => $smaMajor['name'],
                    'code'                 => $smaMajor['code'],
                    'quota'                => $smaMajor['quota'],
                    'description'          => $smaMajor['description'],
                    'requires_health_test' => false,
                    'requires_interview'   => false,
                    'requires_skill_test'  => false,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);

                $quotas = $smaMajor['code'] === 'BHS'
                    ? ['Reguler' => 20, 'Prestasi' => 10, 'Beasiswa' => 6]
                    : ['Reguler' => 40, 'Prestasi' => 20, 'Beasiswa' => 12];

                foreach ($quotas as $trackName => $q) {
                    $trackId = $tracks[$trackName] ?? null;
                    if (! $trackId) {
                        continue;
                    }
                    DB::table('major_track_quotas')->updateOrInsert(
                        ['major_id' => $id, 'registration_track_id' => $trackId],
                        ['quota' => $q, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('majors')) {
            return;
        }

        Schema::table('majors', function (Blueprint $table) {
            $table->dropForeign(['school_level_id']);
            $table->dropColumn('school_level_id');
        });
    }
};
