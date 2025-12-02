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
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345'),
        ]);
        $admin->assignRole('admin');

        $competitor = User::factory()->create([
            'name' => 'Competitor User',
            'email' => 'competitor@example.com',
            'password' => bcrypt('password'),
        ]);
        $competitor->assignRole('competidor');

        $judge = User::factory()->create([
            'name' => 'Judge User',
            'email' => 'judge@example.com',
            'password' => bcrypt('password'),
        ]);
        $judge->assignRole('juez');

        $this->call(CarreraSeeder::class);
        $this->call(EventoSeeder::class);
    }
}
