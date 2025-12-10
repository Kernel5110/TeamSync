<?php

use App\Models\Evento;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$now = now();
echo "Current Time: " . $now . "\n";
echo "--------------------------------------------------\n";
echo "Events Status Check (New Logic):\n";

$eventos = Evento::all();

foreach ($eventos as $evento) {
    echo "ID: " . $evento->id . "\n";
    echo "Name: " . $evento->nombre . "\n";
    echo "Start: " . $evento->fecha_inicio . "\n";
    echo "End:   " . $evento->fecha_fin . "\n";
    
    // New Logic Simulation
    $isVisibleInIndex = $evento->fecha_fin >= $now;
    $canJoinStore = $evento->fecha_fin >= $now;
    
    echo "Visible in Index? " . ($isVisibleInIndex ? "YES" : "NO (Ended)") . "\n";
    echo "Can Join (Store)? " . ($canJoinStore ? "YES" : "NO (Ended)") . "\n";
    echo "--------------------------------------------------\n";
}
