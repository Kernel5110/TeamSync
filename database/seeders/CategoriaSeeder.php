<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Hackathon',
            'Capture The Flag (CTF)',
            'Datathon',
            'Ideathon',
            'Game Jam',
            'Robótica',
            'Programación Competitiva',
            'Ciberseguridad',
            'Innovación Social',
            'Desarrollo Web',
            'Desarrollo Móvil',
            'Inteligencia Artificial',
            'Blockchain',
            'IoT (Internet of Things)',
            'Cloud Computing'
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(['nombre' => $categoria]);
        }
    }
}
