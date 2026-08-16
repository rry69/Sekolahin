<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegistrationTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tracks = [
            ['name' => 'Reguler', 'description' => 'Jalur pendaftaran reguler'],
            ['name' => 'Prestasi', 'description' => 'Jalur prestasi akademik/non-akademik'],
            ['name' => 'Beasiswa', 'description' => 'Jalur beasiswa'],
        ];

        foreach ($tracks as $track) {
            \DB::table('registration_tracks')->insert([
                'name' => $track['name'],
                'description' => $track['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
