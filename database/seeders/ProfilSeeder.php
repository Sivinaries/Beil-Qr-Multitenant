<?php

namespace Database\Seeders;

use App\Models\Profil;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('123456'),
                'level' => 'Admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'afyww18@gmail.com'],
            [
                'name' => 'Afyww',
                'password' => bcrypt('123456'),
                'level' => 'User',
            ]
        );
        
        User::firstOrCreate(
            ['email' => 'didik@gmail.com'],
            [
                'name' => 'Didik',
                'password' => bcrypt('123456'),
                'level' => 'User',
            ]
        );
    }
}
