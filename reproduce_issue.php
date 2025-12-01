<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Create a dummy user or pick one
    $user = User::first();
    if (!$user) {
        echo "No users found. Creating one.\n";
        $user = User::factory()->create();
    }

    echo "Logging in user: " . $user->email . "\n";
    Auth::login($user);

    echo "Attempting to render index view...\n";
    $view = View::make('index')->render();
    echo "View rendered successfully.\n";

} catch (\Throwable $e) {
    echo "Error caught:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
