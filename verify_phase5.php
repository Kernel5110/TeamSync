<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "Verifying Phase 5 Refactor (Admin & Page Cleanup)...\n";

try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();

    // 1. Verify AdminDataController Routes
    $instRoute = $routes->getByName('admin.instituciones.store');
    if ($instRoute && str_contains($instRoute->getAction()['controller'], 'AdminDataController@storeInstitution')) {
         echo "[PASS] Institution Store route correct.\n";
    } else {
         echo "[FAIL] Institution Store route incorrect.\n";
    }

    $careerRoute = $routes->getByName('admin.carreras.store');
    if ($careerRoute && str_contains($careerRoute->getAction()['controller'], 'AdminDataController@storeCareer')) {
         echo "[PASS] Career Store route correct.\n";
    } else {
         echo "[FAIL] Career Store route incorrect.\n";
    }

    // 2. Verify AdminController Teams Route
    $teamsRoute = $routes->getByName('admin.teams');
    if ($teamsRoute && str_contains($teamsRoute->getAction()['controller'], 'AdminController@teams')) {
         echo "[PASS] Admin Teams route moved to AdminController.\n";
    } else {
         echo "[FAIL] Admin Teams route incorrect.\n";
    }

    // 3. Verify PageController Cleanliness
    if (!method_exists(\App\Http\Controllers\PageController::class, 'adminTeams')) {
        echo "[PASS] adminTeams removed from PageController.\n";
    } else {
        echo "[FAIL] adminTeams still exists in PageController.\n";
    }
    
    // 4. Verify AdminController Cleanliness
    if (!method_exists(\App\Http\Controllers\AdminController::class, 'storeInstitucion')) {
         echo "[PASS] storeInstitucion removed from AdminController.\n";
    } else {
         echo "[FAIL] storeInstitucion still exists in AdminController.\n";
    }

    echo "Phase 5 Verification Complete.\n";

} catch (\Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
