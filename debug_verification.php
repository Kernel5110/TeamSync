<?php

use App\Models\Evento;
use App\Models\Categoria;
use App\Models\Institucion;
use App\Models\Carrera;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Verifying Admin Enhancements ---\n";

// 1. Verify Tables
echo "Checking tables...\n";
if (Schema::hasTable('categorias') && Schema::hasTable('instituciones') && Schema::hasTable('categoria_evento')) {
    echo "Tables exist.\n";
} else {
    echo "ERROR: Tables missing.\n";
    exit(1);
}

// 2. Verify Schools and Careers
echo "Creating School and Career...\n";
$inst = Institucion::create(['nombre' => 'Test Institute ' . uniqid()]);
$carrera = Carrera::create(['nombre' => 'Test Career ' . uniqid()]);
echo "Created Institution: {$inst->nombre}\n";
echo "Created Career: {$carrera->nombre}\n";

// 3. Verify Event with Manual Status and Categories
echo "Creating Event with Manual Status and Categories...\n";
$cat1 = Categoria::firstOrCreate(['nombre' => 'Test Cat 1']);
$cat2 = Categoria::firstOrCreate(['nombre' => 'Test Cat 2']);

$evento = Evento::create([
    'nombre' => 'Test Event ' . uniqid(),
    'descripcion' => 'Test Description',
    'fecha_inicio' => now()->addDays(1),
    'fecha_fin' => now()->addDays(2),
    'start_time' => '10:00',
    'ubicacion' => 'Test Location',
    'capacidad' => 100,
    'status_manual' => 'Finalizado', // Manual override
]);

$evento->categorias()->attach([$cat1->id, $cat2->id]);

echo "Event Created: {$evento->nombre}\n";
echo "Manual Status: {$evento->status_manual}\n";
echo "Computed Status (should be Finalizado): {$evento->status}\n";

if ($evento->status === 'Finalizado') {
    echo "SUCCESS: Manual status works.\n";
} else {
    echo "FAILURE: Manual status check failed. Got: {$evento->status}\n";
}

echo "Categories: " . $evento->categorias->pluck('nombre')->implode(', ') . "\n";
if ($evento->categorias->count() === 2) {
    echo "SUCCESS: Multiple categories attached.\n";
} else {
    echo "FAILURE: Category attachment failed.\n";
}

// Cleanup
$evento->delete();
$inst->delete();
$carrera->delete();
$cat1->delete();
$cat2->delete();
echo "Cleanup done.\n";
