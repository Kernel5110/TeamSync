<?php

use App\Models\Event;
use App\Models\Team;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying Event Refactor...\n";

try {
    // Create an event
    $event = Event::create([
        'name' => 'Test Event',
        'description' => 'Test Description',
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDays(2),
        'location' => 'Test Location',
        'capacity' => 100,
        'status_manual' => 'Próximo'
    ]);

    echo "[PASS] Event created: {$event->id} - {$event->name}\n";

    // Create a team
    $team = Team::create([
        'name' => 'Test Team',
        'event_id' => $event->id,
        'project_name' => 'Test Project'
    ]);

    echo "[PASS] Team created: {$team->id} - {$team->name}\n";

    // Test relationship
    $loadedEvent = Event::with('teams')->find($event->id);
    if ($loadedEvent->teams->contains($team->id)) {
        echo "[PASS] Event->teams relationship works.\n";
    } else {
        echo "[FAIL] Event->teams relationship failed.\n";
    }

    // Cleanup
    $team->delete();
    $event->delete();
    echo "Cleanup done.\n";

} catch (\Exception $e) {
    echo "[FAIL] Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
