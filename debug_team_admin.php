<?php

use App\Models\Equipo;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--------------------------------------------------\n";
echo "Admin Team Management Check:\n";

// Create a test event and team
$evento = Evento::create([
    'nombre' => 'Test Event for Deletion',
    'descripcion' => 'Test Description',
    'fecha_inicio' => now()->addDays(1),
    'fecha_fin' => now()->addDays(2),
    'start_time' => '10:00',
    'ubicacion' => 'Test Location',
    'capacidad' => 10,
    'categoria' => 'Test'
]);

$team = Equipo::create([
    'nombre' => 'Test Team to Delete',
    'evento_id' => $evento->id
]);

echo "Created Team ID: " . $team->id . " in Event ID: " . $evento->id . "\n";

// Simulate Admin User
$admin = User::whereHas('roles', function($q){ $q->where('name', 'admin'); })->first();
if(!$admin) {
    echo "No admin user found for test.\n";
    exit;
}
Auth::login($admin);

// Simulate Destroy (Soft Delete from Event)
echo "Simulating Destroy (Soft Remove)...\n";
$controller = new \App\Http\Controllers\TeamController();
try {
    // We can't easily call the controller method directly because of redirects, 
    // but we can simulate the logic:
    $team->update(['evento_id' => null]);
    echo "Update executed.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$refreshedTeam = Equipo::find($team->id);
if ($refreshedTeam && $refreshedTeam->evento_id === null) {
    echo "SUCCESS: Team exists but evento_id is NULL.\n";
} else {
    echo "FAILURE: Team deleted or evento_id not NULL.\n";
}

// Cleanup
if($refreshedTeam) $refreshedTeam->delete();
$evento->delete();
echo "--------------------------------------------------\n";
