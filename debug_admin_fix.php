<?php

use App\Models\User;
use App\Models\Evento;
use App\Models\Equipo;
use App\Models\Participante;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Verifying Admin Fixes & Participation Logic ---\n";

// 1. Verify Admin User Update Route
echo "Checking Admin User Update Route...\n";
$routes = Illuminate\Support\Facades\Route::getRoutes();
$route = $routes->getByName('admin.users.update');
if ($route) {
    echo "SUCCESS: Route 'admin.users.update' exists.\n";
} else {
    echo "FAILURE: Route 'admin.users.update' missing.\n";
}

// 2. Verify Participation Logic (Simulation)
// We can't easily simulate the full HTTP request flow here without a test database state, 
// but we can check if the code in TeamController has the new logic.
// Or we can try to create a scenario.

echo "Simulating Participation Check...\n";
// Create User
$user = User::create([
    'name' => 'Test User ' . uniqid(),
    'email' => 'test' . uniqid() . '@example.com',
    'password' => bcrypt('password'),
]);
// $user->assignRole('participante'); // Role might not exist, skipping

// Create Carrera
$carrera = \App\Models\Carrera::create(['nombre' => 'Test Career ' . uniqid()]);

// Create Participant Profile
$participante = Participante::create([
    'usuario_id' => $user->id,
    'carrera_id' => $carrera->id,
    'num_control' => '12345678',
    'institucion' => 'Test Inst',
    'equipo_id' => null,
    'rol' => null,
]);

// Create Active Event
$activeEvent = Evento::create([
    'nombre' => 'Active Event ' . uniqid(),
    'descripcion' => 'Desc',
    'fecha_inicio' => now()->subDay(),
    'fecha_fin' => now()->addDay(),
    'start_time' => '10:00',
    'ubicacion' => 'Loc',
    'capacidad' => 10,
    'status_manual' => 'En Curso',
]);

// Create Team in Active Event
$team1 = Equipo::create([
    'nombre' => 'Team 1 ' . uniqid(),
    'evento_id' => $activeEvent->id,
]);

// Add User to Team 1
$participante->update(['equipo_id' => $team1->id]);

// Create Another Event
$newEvent = Evento::create([
    'nombre' => 'New Event ' . uniqid(),
    'descripcion' => 'Desc',
    'fecha_inicio' => now()->addDays(5),
    'fecha_fin' => now()->addDays(6),
    'start_time' => '10:00',
    'ubicacion' => 'Loc',
    'capacidad' => 10,
]);

// Create Team in New Event
$team2 = Equipo::create([
    'nombre' => 'Team 2 ' . uniqid(),
    'evento_id' => $newEvent->id,
]);

// Try to join Team 2 (Should fail logic check, but we need to call the controller method or replicate logic)
// Replicating logic:
$userTeams = Equipo::whereHas('participantes', function($q) use ($user) {
    $q->where('usuario_id', $user->id);
})->with('evento')->get();

$blocked = false;
foreach ($userTeams as $team) {
    if ($team->evento && $team->evento->status === 'En Curso') {
        $blocked = true;
        echo "Blocked by event: " . $team->evento->nombre . "\n";
    }
}

if ($blocked) {
    echo "SUCCESS: Participation logic correctly identifies active event conflict.\n";
} else {
    echo "FAILURE: Participation logic failed to identify conflict.\n";
}

// Cleanup
$participante->delete();
$user->delete();
$team1->delete();
$team2->delete();
$activeEvent->delete();
$newEvent->delete();

echo "Cleanup done.\n";
