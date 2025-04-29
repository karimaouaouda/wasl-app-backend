<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'role' => 'user',
            'name' => 'حذيفة المشرقي',
            'username' => 'Hudhaifa Almashraqi',
            'email' => 'Hudhaifa@example.com',
            'password' => bcrypt('Aa223344'),
        ]);

        User::create([
            'role' => 'user',
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('Aa223344'),
        ]);
        User::create([
            'role' => 'admin',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('Aa223344'),
        ]);
    }
}
