<?php

use App\Models\User;
use App\Models\Participant;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying User Relationship...\n";

// Create a dummy user and participant
try {
    $user = User::factory()->create();
    $participant = Participant::create([
        'user_id' => $user->id,
        'career_id' => 1, // Assuming 1 exists or is nullable/defaulted
        'institution' => 'Test Inst',
        'control_number' => '123456'
    ]);

    echo "User created: {$user->id}\n";
    echo "Participant created: {$participant->id} for User: {$participant->user_id}\n";

    // Test relationship
    $loadedUser = User::find($user->id);
    if ($loadedUser->participant && $loadedUser->participant->id === $participant->id) {
        echo "[PASS] User->participant relationship works.\n";
    } else {
        echo "[FAIL] User->participant relationship failed.\n";
        if ($loadedUser->participant) {
             echo "Loaded participant ID: " . $loadedUser->participant->id . "\n";
        } else {
             echo "Loaded participant is null.\n";
        }
    }

} catch (\Exception $e) {
    echo "[FAIL] Exception: " . $e->getMessage() . "\n";
}

// Cleanup
if (isset($participant)) $participant->delete();
if (isset($user)) $user->delete();
echo "Cleanup done.\n";
