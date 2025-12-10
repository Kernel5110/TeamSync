<?php

namespace Database\Seeders;

use App\Models\Institucion;
use Illuminate\Database\Seeder;

class InstitucionSeeder extends Seeder
{
    public function run()
    {
        $instituciones = [
            'Instituto Tecnológico de Oaxaca',
            'Instituto Tecnológico de Durango',
            'Instituto Tecnológico de Aguascalientes',
            'Instituto Tecnológico de Morelia',
            'Instituto Tecnológico de Tijuana',
            'Instituto Tecnológico de Hermosillo',
            'Instituto Tecnológico de Mérida',
            'Instituto Tecnológico de Tuxtla Gutiérrez',
            'Instituto Tecnológico de Veracruz',
            'Instituto Tecnológico de Toluca',
            'Instituto Tecnológico de Celaya',
            'Instituto Tecnológico de Culiacán',
            'Instituto Tecnológico de Chihuahua',
            'Instituto Tecnológico de Ciudad Juárez',
            'Instituto Tecnológico de Saltillo',
            'Instituto Tecnológico de San Luis Potosí',
            'Instituto Tecnológico de Querétaro',
            'Instituto Tecnológico de Puebla',
            'Instituto Tecnológico de Pachuca',
            'Instituto Tecnológico de Tepic',
        ];

        foreach ($instituciones as $nombre) {
            Institucion::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
