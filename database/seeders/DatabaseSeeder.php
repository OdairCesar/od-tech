<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Senha fixa ("password") por conveniência local — este seeder nunca deve rodar em produção.
        User::factory()->admin()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            ServiceSeeder::class,
            CitySeeder::class,
        ]);
    }
}
