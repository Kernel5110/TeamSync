<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--------------------------------------------------\n";
echo "Admin Capabilities Check:\n";

// 1. Check if routes exist (basic check)
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$adminUsersRoute = $routes->getByName('admin.users');
$adminUsersStoreRoute = $routes->getByName('admin.users.store');
$adminUsersDestroyRoute = $routes->getByName('admin.users.destroy');

echo "Route 'admin.users' exists? " . ($adminUsersRoute ? "YES" : "NO") . "\n";
echo "Route 'admin.users.store' exists? " . ($adminUsersStoreRoute ? "YES" : "NO") . "\n";
echo "Route 'admin.users.destroy' exists? " . ($adminUsersDestroyRoute ? "YES" : "NO") . "\n";

// 2. Simulate User Creation
echo "--------------------------------------------------\n";
echo "Simulating User Creation...\n";
$testEmail = 'test_admin_creation@example.com';
$existing = User::where('email', $testEmail)->first();
if ($existing) $existing->delete();

$user = User::create([
    'name' => 'Test Admin Created',
    'email' => $testEmail,
    'password' => Hash::make('password'),
]);
$user->assignRole('juez');

echo "User created: " . $user->name . " (" . $user->email . ")\n";
echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";

// 3. Simulate User Deletion
echo "--------------------------------------------------\n";
echo "Simulating User Deletion...\n";
$user->delete();
$deleted = User::where('email', $testEmail)->first();
echo "User deleted? " . (!$deleted ? "YES" : "NO") . "\n";

echo "--------------------------------------------------\n";
