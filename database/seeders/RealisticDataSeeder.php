<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Evento;
use App\Models\Equipo;
use App\Models\Participante;
use Spatie\Permission\Models\Role;

class RealisticDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist (just in case)
        if (Role::count() == 0) {
            $this->call(RoleSeeder::class);
        }

        // Get all events
        $eventos = Evento::all();

        if ($eventos->isEmpty()) {
            $this->call(EventoSeeder::class);
            $eventos = Evento::all();
        }

        $teamNames = [
            'Quantum Coders', 'Neural Networks', 'Binary Bandits', 'Pixel Pioneers', 
            'Cloud Chasers', 'Data Dynamos', 'Cyber Sentinels', 'Logic Legends',
            'Syntax Squad', 'Algorithm Avengers', 'Infinite Loopers', 'Stack Overflowers'
        ];

        foreach ($eventos as $evento) {
            // Create 3-5 teams per event
            $numTeams = rand(3, 5);
            
            for ($i = 0; $i < $numTeams; $i++) {
                $teamName = $teamNames[array_rand($teamNames)] . ' ' . rand(1, 99);
                
                $equipo = Equipo::create([
                    'nombre' => $teamName,
                    'evento_id' => $evento->id,
                    'submission_path' => null, // Or a fake path
                ]);

                // Create 2-4 members per team
                $numMembers = rand(2, 4);
                for ($j = 0; $j < $numMembers; $j++) {
                    $user = User::factory()->create();
                    $user->assignRole('competidor');

                    // Get a random carrera
                    $carreraId = \App\Models\Carrera::inRandomOrder()->first()->id ?? 1;

                    Participante::create([
                        'usuario_id' => $user->id,
                        'equipo_id' => $equipo->id,
                        'carrera_id' => $carreraId,
                        'institucion' => 'TecNM',
                        'num_control' => rand(10000000, 99999999),
                    ]);
                }
            }
        }

        // Assign Judge to the first event
        $judgeUser = User::where('email', 'juez@gmail.com')->first();
        if ($judgeUser && $eventos->isNotEmpty()) {
            $eventos->first()->jueces()->syncWithoutDetaching([$judgeUser->id]);
        }
    }
}
