<?php

use App\Models\Event;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$now = now();
echo "Current Time: " . $now . "\n";
echo "--------------------------------------------------\n";
echo "Events Status Check (Refactored):\n";

$events = Event::all();

foreach ($events as $event) {
    echo "ID: " . $event->id . "\n";
    echo "Name: " . $event->name . "\n";
    echo "Start: " . $event->starts_at . "\n";
    echo "End:   " . $event->ends_at . "\n";
    
    // Status Logic check
    echo "Status: " . $event->status . "\n";
    echo "--------------------------------------------------\n";
}
