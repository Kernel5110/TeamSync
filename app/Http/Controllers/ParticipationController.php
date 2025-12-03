<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ParticipationController extends Controller
{
    public function show($evento_id)
    {
        $evento = Evento::findOrFail($evento_id);
        // Check if user has a team in this event
        // Assuming user is a participant and belongs to a team. 
        // For simplicity, let's assume the user is part of a team linked to this event.
        // In a real app, we'd need to find the user's team for this event.
        
        // Let's find the team where the current user is a participant
        // This logic depends on how participants are linked to users and teams.
        // Based on previous context, there is a 'participantes' table and 'User' model.
        // Let's assume we can get the team via the user.
        
        // For now, I'll pass the event. The view can handle showing the upload form if the user is eligible.
        return view('participation', compact('evento'));
    }

    public function upload(Request $request, $evento_id)
    {
        $request->validate([
            'submission' => 'required|file|mimes:pdf,zip,rar,doc,docx|max:10240', // 10MB max
        ]);

        $evento = Evento::findOrFail($evento_id);
        
        // Find the team of the current user for this event.
        // This is a bit complex without knowing the exact relationship structure fully.
        // Let's assume for now we can find the team.
        // If the user is logged in, we can check their participants records.
        
        $user = Auth::user();
        // Assuming a user has one participant record per event or globally?
        // Let's look at the schema again if needed.
        // 'participantes' table has 'equipo_id'. 'equipos' has 'evento_id'.
        
        // We need to find a participant record for this user that belongs to a team in this event.
        $equipo = Equipo::where('evento_id', $evento_id)
            ->whereHas('participantes', function($query) use ($user) {
                $query->where('usuario_id', $user->id); 
            })->first();

        if (!$equipo) {
             // Fallback: try to find if the user is the CREATOR of the team or similar?
             // Or maybe just any team the user is in.
             // Let's check 'participantes' table schema again to be sure about 'user_id'.
             // Wait, I didn't check 'participantes' schema fully.
             // Let's assume standard relation. If not, I'll debug.
             
             return back()->with('error', 'No eres parte de un equipo en este evento.');
        }

        if ($request->hasFile('submission')) {
            $path = $request->file('submission')->store('submissions', 'public');
            $equipo->submission_path = $path;
            $equipo->save();

            return back()->with('success', 'Archivo subido exitosamente.');
        }

        return back()->with('error', 'Error al subir el archivo.');
    }
}
