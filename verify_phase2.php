<?php

use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use App\Models\Participant;
use App\Models\Evaluation;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying Phase 2 Refactor...\n";

try {
    // 1. Setup User and Participant
    $user = User::factory()->create();
    
    // Ensure Career exists
    $career = \App\Models\Career::first();
    if (!$career) {
        $inst = \App\Models\Institution::first();
        if (!$inst) {
            $inst = \App\Models\Institution::create(['name' => 'Test Inst']);
        }
        $career = \App\Models\Career::create(['name' => 'Test Career', 'institution_id' => $inst->id]);
    }

    // Ensure participant exists (User factory might not create it)
    if (!$user->participant) {
        $participant = Participant::create([
            'user_id' => $user->id,
            'career_id' => $career->id,
            'institution' => 'Test Institution', // It seems to store the name directly or expects a value
            'control_number' => '12345678' // Adding control number just in case
        ]);
    } else {
        $participant = $user->participant;
    }
    
    // 2. Setup Events
    $activeEvent = Event::create([
        'name' => 'Active Event',
        'description' => 'Desc',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'location' => 'Loc',
        'capacity' => 10,
        'status_manual' => 'En Curso'
    ]);
    
    $futureEvent = Event::create([
        'name' => 'Future Event',
        'description' => 'Desc',
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(10),
        'location' => 'Loc',
        'capacity' => 10,
        'status_manual' => 'Próximo'
    ]);

    // 3. Test active_event
    echo "Testing User active_event...\n";
    $team = Team::create([
        'name' => 'Team Alpha',
        'event_id' => $activeEvent->id
    ]);
    
    $participant->update(['team_id' => $team->id]);
    $user->refresh(); // Reload relations
    
    if ($user->active_event && $user->active_event->id == $activeEvent->id) {
        echo "[PASS] User active_event correctly identifies current event.\n";
    } else {
        echo "[FAIL] User active_event failed. Got: " . ($user->active_event ? $user->active_event->name : 'null') . "\n";
    }
    
    // Test with inactive event
    $activeEvent->update(['status_manual' => 'Finalizado']);
    $user->refresh(); // active_event attribute might cache? No, it's method. But relation might cache.
    // Actually attribute is not cached unless we used getAttribute.
    // However, relation $participant->team->event might be cached.
    // Let's refresh user->participant->team->event?
    $user = User::find($user->id); // Fresh user
    
    if ($user->active_event === null) {
        echo "[PASS] User active_event returns null for finalized event.\n";
    } else {
        echo "[FAIL] User active_event should be null. Got: " . $user->active_event->name . "\n";
    }

    // 4. Test Ranking
    echo "Testing Event Ranking...\n";
    $activeEvent->update(['status_manual' => 'En Curso']); // Restore
    
    // Create Criterion
    $crit1 = \App\Models\Criterion::create(['event_id' => $activeEvent->id, 'name' => 'Innovation', 'max_score' => 10]);
    $crit2 = \App\Models\Criterion::create(['event_id' => $activeEvent->id, 'name' => 'Impact', 'max_score' => 10]);
    $crit3 = \App\Models\Criterion::create(['event_id' => $activeEvent->id, 'name' => 'Technical', 'max_score' => 10]);

    $team2 = Team::create([
        'name' => 'Team Beta',
        'event_id' => $activeEvent->id
    ]);
    
    // Add evaluations with SCORES
    // Team Alpha: 10 + 10 + 10 = 30
    $eval1 = Evaluation::create([
        'user_id' => $user->id,
        'team_id' => $team->id,
        'event_id' => $activeEvent->id,
        // Legacy columns can be 0 or null
        'score_innovation' => 0,
        'score_social_impact' => 0,
        'score_technical_viability' => 0,
    ]);
    
    \App\Models\EvaluationScore::create(['evaluation_id' => $eval1->id, 'criterion_id' => $crit1->id, 'score' => 10]);
    \App\Models\EvaluationScore::create(['evaluation_id' => $eval1->id, 'criterion_id' => $crit2->id, 'score' => 10]);
    \App\Models\EvaluationScore::create(['evaluation_id' => $eval1->id, 'criterion_id' => $crit3->id, 'score' => 10]);
    
    // Team Beta: 5 + 5 + 5 = 15
     $eval2 = Evaluation::create([
        'user_id' => $user->id,
        'team_id' => $team2->id,
        'event_id' => $activeEvent->id,
        'score_innovation' => 0,
        'score_social_impact' => 0,
        'score_technical_viability' => 0,
    ]);
    
    \App\Models\EvaluationScore::create(['evaluation_id' => $eval2->id, 'criterion_id' => $crit1->id, 'score' => 5]);
    \App\Models\EvaluationScore::create(['evaluation_id' => $eval2->id, 'criterion_id' => $crit2->id, 'score' => 5]);
    \App\Models\EvaluationScore::create(['evaluation_id' => $eval2->id, 'criterion_id' => $crit3->id, 'score' => 5]);
    
    $ranking = $activeEvent->getRanking();
    
    // Total for Alpha = 30. Avg = 30 (1 judge).
    // Total for Beta = 15. Avg = 15.
    
    if ($ranking->count() == 2 && $ranking[0]->id == $team->id) {
        echo "[PASS] Ranking order correct (Alpha first).\n";
        echo "Alpha Score: " . $ranking[0]->total_score . "\n";
    } else {
        echo "[FAIL] Ranking order incorrect.\n";
        print_r($ranking->pluck('name', 'total_score'));
    }

    // Cleanup
    // Logic to cleanup created data
    // Optional
    
} catch (\Exception $e) {
    echo "[FAIL] Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

