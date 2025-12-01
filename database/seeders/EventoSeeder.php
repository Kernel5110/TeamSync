<?php

namespace Database\Seeders;

use App\Models\Evento;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        Evento::create([
            'nombre' => 'HACKATEC',
            'descripcion' => 'Desarrollar proyectos de base tecnológica y creativos con características de escalabilidad que incentiven las capacidades de investigación y desarrollo tecnológico en la solución de problemas.',
            'fecha_inicio' => '2024-03-15',
            'fecha_fin' => '2024-03-17',
            'ubicacion' => 'Campus TecNM',
            'capacidad' => '50+ equipos',
        ]);

        Evento::create([
            'nombre' => 'INNOVATEC',
            'descripcion' => 'Desarrollar proyectos de base tecnológica y creativos con características de escalabilidad que incentiven las capacidades de investigación y desarrollo tecnológico en la solución de problemas.',
            'fecha_inicio' => '2024-04-22',
            'fecha_fin' => '2024-04-24',
            'ubicacion' => 'Centro de Innovación',
            'capacidad' => '40+ equipos',
        ]);
    }
}
