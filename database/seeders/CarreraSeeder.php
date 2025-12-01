<?php

namespace Database\Seeders;

use App\Models\Carrera;
use Illuminate\Database\Seeder;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = [
            'Ing. Sistemas',
            'Ing. Civil',
            'Ing. Electrónica',
            'Ing. Gestión Empresarial',
            'Ing. Industrial',
            'Ing. Mecánica',
            'Ing. Química',
            'Lic. Administración',
        ];

        foreach ($carreras as $carrera) {
            Carrera::create(['nombre' => $carrera]);
        }
    }
}
