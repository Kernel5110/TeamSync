<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Evento;
use App\Models\Equipo;
use App\Models\Participante;
use App\Models\Evaluation;
use App\Models\Solicitud;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

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

        $technologiesList = [
            'Laravel, Vue.js, MySQL', 'React, Node.js, MongoDB', 'Python, Django, PostgreSQL',
            'Flutter, Firebase', 'Angular, Spring Boot, Oracle', 'Swift, SwiftUI',
            'Kotlin, Jetpack Compose', 'Rust, WebAssembly', 'Go, Docker, Kubernetes'
        ];

        $descriptions = [
            'Una plataforma innovadora para la gestión de recursos hídricos en comunidades rurales.',
            'Sistema de detección temprana de incendios forestales utilizando IoT y Machine Learning.',
            'Aplicación móvil para conectar a productores locales con consumidores finales sin intermediarios.',
            'Herramienta de educación financiera gamificada para jóvenes universitarios.',
            'Solución de telemedicina para zonas de difícil acceso con diagnósticos preliminares por IA.',
            'Marketplace descentralizado basado en Blockchain para artistas digitales.',
            'Sistema de optimización de rutas de transporte público en tiempo real.',
            'Plataforma de realidad aumentada para el aprendizaje interactivo de historia y cultura.'
        ];

        // Ensure Judge exists
        $judgeUser = User::where('email', 'juez@gmail.com')->first();
        if (!$judgeUser) {
             $judgeUser = User::factory()->create([
                'name' => 'Juez Principal',
                'email' => 'juez@gmail.com',
                'password' => Hash::make('12345'),
            ]);
            $judgeUser->assignRole('juez');
        }

        foreach ($eventos as $evento) {
            // Assign Judge to all events
            $evento->jueces()->syncWithoutDetaching([$judgeUser->id]);

            // Create 3-5 teams per event
            $numTeams = rand(3, 5);
            
            for ($i = 0; $i < $numTeams; $i++) {
                $teamName = $teamNames[array_rand($teamNames)] . ' ' . rand(1, 99);
                $tech = $technologiesList[array_rand($technologiesList)];
                $desc = $descriptions[array_rand($descriptions)];
                
                $equipo = Equipo::create([
                    'nombre' => $teamName,
                    'evento_id' => $evento->id,
                    'submission_path' => null,
                    'project_name' => 'Proyecto ' . $teamName,
                    'technologies' => $tech,
                    'project_description' => $desc,
                    'github_repo' => 'https://github.com/example/' . \Illuminate\Support\Str::slug($teamName),
                    'github_pages' => 'https://example.github.io/' . \Illuminate\Support\Str::slug($teamName),
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
                        'rol' => ($j == 0) ? 'Líder' : 'Miembro', // First member is Leader
                    ]);
                }

                // Create Evaluations if event is 'Finalizado' (or just for some teams)
                // HACKATEC 2025 is in the past (Jan 2025)
                if ($evento->status === 'Finalizado' || $evento->nombre === 'HACKATEC 2025') {
                    Evaluation::create([
                        'equipo_id' => $equipo->id,
                        'user_id' => $judgeUser->id,
                        'evento_id' => $evento->id,
                        'score_innovation' => rand(5, 10),
                        'score_social_impact' => rand(5, 10),
                        'score_technical_viability' => rand(5, 10),
                        'comments' => 'Excelente proyecto, muy buena implementación técnica y gran impacto social.',
                    ]);
                }

                // Create some Join Requests (Solicitudes)
                if (rand(0, 1)) {
                    $requester = User::factory()->create();
                    $requester->assignRole('competidor');
                    // Create participant profile for requester
                    Participante::create([
                        'usuario_id' => $requester->id,
                        'equipo_id' => null,
                        'carrera_id' => 1,
                        'institucion' => 'TecNM',
                        'num_control' => rand(10000000, 99999999),
                    ]);

                    Solicitud::create([
                        'user_id' => $requester->id,
                        'equipo_id' => $equipo->id,
                        'status' => ['pending', 'accepted', 'rejected'][rand(0, 2)],
                    ]);
                }
            }
        }
    }
}
