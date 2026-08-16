<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereIn('role_id', [2, 3])->delete();
        
        DB::table('roles')->whereIn('id', [2, 3])->delete();
        
        DB::table('roles')->where('id', 4)->update([
            'name' => 'Siswa',
            'description' => 'Siswa/Calon siswa',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('roles')->where('id', 4)->update([
            'name' => 'Applicant',
            'description' => 'Calon siswa/wali murid',
            'updated_at' => now(),
        ]);
        
        DB::table('roles')->insert([
            ['id' => 2, 'name' => 'Panitia', 'description' => 'Panitia verifikasi dan manajemen seleksi', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Keuangan', 'description' => 'Verifikasi pembayaran', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
