<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Order matters: Cities & Categories → Projects (with Properties inside) → Teams → Partners
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'role' => 'admin',
            'status' => true,
            'password' => '1234567890',
        ]);

        $this->call(CitySeeder::class);
        $this->call(PropertyTypeSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(PropertySeeder::class);
        $this->call(TeamSeeder::class);
        $this->call(SettingSeeder::class);
    }
}
