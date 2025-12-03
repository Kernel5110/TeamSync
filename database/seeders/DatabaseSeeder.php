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
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345'),
        ]);
        $admin->assignRole('admin');

        $competitor = User::factory()->create([
            'name' => 'Participante Prueba',
            'email' => 'participante@gmail.com',
            'password' => bcrypt('12345'),
        ]);
        $competitor->assignRole('competidor');

        $judge = User::factory()->create([
            'name' => 'Juez Principal',
            'email' => 'juez@gmail.com',
            'password' => bcrypt('12345'),
        ]);
        $judge->assignRole('juez');

        $this->call(CarreraSeeder::class);
        $this->call(EventoSeeder::class);
        $this->call(RealisticDataSeeder::class);
    }
}
