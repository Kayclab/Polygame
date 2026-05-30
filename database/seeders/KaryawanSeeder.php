<?php

namespace Database\Seeders;

use App\Models\karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        karyawan::create([
            'n_kry' => 'Choirun Kenji Morales',
            'email' => 'serigala.albino10@gmail.com',
            'password' => Hash::make('admin123'),
            'jab' => 'Owner',
            'alamat' => 'Arzimar',
            'tmpt_lahir' => 'Madura',
            'tgl_lahir' => now(),
            'tgl_mulai_kerja' => now(),
            'role' => 'owner',
        ]);
        karyawan::create([
            'n_kry' => 'Kaila Puteri Iskandar',
            'email' => 'adzkian127@gmail.com',
            'password' => Hash::make('barista123'),
            'jab' => 'Barista',
            'alamat' => 'Arzimar',
            'tmpt_lahir' => 'Madura',
            'tgl_lahir' => now(),
            'tgl_mulai_kerja' => now(),
            'role' => 'staff',
        ]);
        karyawan::create([
            'n_kry' => 'Dila Qonita Hidayati',
            'email' => 'adzghan@gmail.com',
            'password' => Hash::make('gm123'),
            'jab' => 'Game Master',
            'alamat' => 'Arzimar',
            'tmpt_lahir' => 'Madura',
            'tgl_lahir' => now(),
            'tgl_mulai_kerja' => now(),
            'role' => 'staff',
        ]);
    }
}
