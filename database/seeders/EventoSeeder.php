<?php

namespace Database\Seeders;

use App\Models\Evento;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        Evento::create([
            'nombre' => 'HACKATEC 2025',
            'descripcion' => 'El hackathon más grande de la región. 48 horas de programación intensa para resolver desafíos de la industria real. Categorías: Fintech, Healthtech, y Edtech.',
            'fecha_inicio' => '2025-03-15',
            'fecha_fin' => '2025-03-17',
            'ubicacion' => 'Campus Central - Auditorio Principal',
            'capacidad' => '50 equipos',
        ]);

        Evento::create([
            'nombre' => 'INNOVATEC Summit',
            'descripcion' => 'Feria de innovación tecnológica donde estudiantes presentan prototipos funcionales. Oportunidad de networking con inversores y líderes de la industria.',
            'fecha_inicio' => '2025-04-22',
            'fecha_fin' => '2025-04-24',
            'ubicacion' => 'Centro de Convenciones',
            'capacidad' => '40 equipos',
        ]);

        Evento::create([
            'nombre' => 'AI Challenge',
            'descripcion' => 'Competencia de Inteligencia Artificial. Desarrolla modelos predictivos y soluciones basadas en Machine Learning para problemas sociales.',
            'fecha_inicio' => '2025-05-10',
            'fecha_fin' => '2025-05-12',
            'ubicacion' => 'Laboratorio de Cómputo Avanzado',
            'capacidad' => '30 equipos',
        ]);

        Evento::create([
            'nombre' => 'CyberSecurity CTF',
            'descripcion' => 'Capture The Flag. Demuestra tus habilidades en seguridad informática, criptografía y hacking ético en este emocionante desafío.',
            'fecha_inicio' => '2025-06-05',
            'fecha_fin' => '2025-06-06',
            'ubicacion' => 'Virtual / Sala de Redes',
            'capacidad' => '100 participantes',
        ]);
    }
}
