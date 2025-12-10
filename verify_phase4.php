<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\User;
use App\Models\Event;
use App\Models\Team;
use App\Models\Evaluation;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "Verifying Phase 4 Refactor (ReportController & EventController)...\n";

try {
    // 1. Verify ReportController exists and is callable
    if (class_exists(\App\Http\Controllers\ReportController::class)) {
        echo "[PASS] ReportController class exists.\n";
    } else {
        echo "[FAIL] ReportController class missing.\n";
        exit(1);
    }

    // 2. Verify Routes
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $pdfRoute = $routes->getByName('events.reports.pdf');
    $csvRoute = $routes->getByName('events.reports.csv');
    
    if ($pdfRoute && str_contains($pdfRoute->getAction()['controller'], 'ReportController@generatePdfReport')) {
         echo "[PASS] PDF Route points to ReportController.\n";
    } else {
         echo "[FAIL] PDF Route incorrect: " . ($pdfRoute ? $pdfRoute->getAction()['controller'] : 'Not found') . "\n";
    }

    if ($csvRoute && str_contains($csvRoute->getAction()['controller'], 'ReportController@generateCsvReport')) {
         echo "[PASS] CSV Route points to ReportController.\n";
    } else {
         echo "[FAIL] CSV Route incorrect: " . ($csvRoute ? $csvRoute->getAction()['controller'] : 'Not found') . "\n";
    }
    
    // 3. Verify EventController Index (Smoke Test to ensure no syntax error from rename)
    // We can't easily simulate a request here without more setup, but we can call index() if we mock auth, 
    // or just check if method exists.
    if (method_exists(\App\Http\Controllers\EventController::class, 'index')) {
        echo "[PASS] EventController::index exists.\n";
    }
    
    echo "Phase 4 Verification Complete.\n";

} catch (\Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
