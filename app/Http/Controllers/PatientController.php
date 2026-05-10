<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Resultat;
use App\Models\RendezVous;

class PatientController extends Controller
{
    public function index()
    {
        if (!session('patient_id')) {
            return redirect()->route('login.patient');
        }

        $resultats = Resultat::where('patient_id', session('patient_id'))
            ->orderByDesc('id')
            ->get();

        $rendezvous = RendezVous::where('patient_id', session('patient_id'))
            ->where('date_rdv', '>=', now()->toDateString())
            ->where('statut', '!=', 'annulé')
            ->orderBy('date_rdv', 'asc')
            ->get();

        return view('espace_patient', compact('resultats', 'rendezvous'));
    }

    public function reserverRdv(Request $request)
    {
        if (!session('patient_id')) {
            return redirect()->route('login.patient');
        }

        $request->validate([
            'date_rdv' => 'required|date',
            'heure_rdv' => 'required',
            'motif' => 'nullable|string'
        ]);

        $dateChoisie = Carbon::parse($request->date_rdv);
        $today = Carbon::today();
        $maxDate = Carbon::today()->addDays(20);

        if ($dateChoisie->lt($today) || $dateChoisie->gt($maxDate)) {
            return back()->with('error', 'Vous pouvez réserver seulement dans les 20 prochains jours.');
        }

        $exists = RendezVous::where('date_rdv', $request->date_rdv)
            ->where('heure_rdv', $request->heure_rdv)
            ->whereIn('statut', ['en_attente', 'accepté'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ce créneau est déjà réservé.');
        }

        RendezVous::create([
            'patient_id' => session('patient_id'),
            'date_rdv' => $request->date_rdv,
            'heure_rdv' => $request->heure_rdv,
            'motif' => $request->motif,
            'statut' => 'en_attente',
        ]);

        return back()->with('success', 'Rendez-vous réservé avec succès.');
    }

    public function annulerRdv($id)
    {
        if (!session('patient_id')) {
            return redirect()->route('login.patient');
        }

        RendezVous::where('id', $id)
        ->where('patient_id', session('patient_id'))
        ->delete();

        return back()->with('success', 'Rendez-vous annulé avec succès.');
    }

    public function availableSlots(Request $request)
    {
        $date = $request->date;

        $start = strtotime('09:00');
        $end = strtotime('17:00');
        $duration = 20 * 60;

        $reserved = RendezVous::where('date_rdv', $date)
            ->whereIn('statut', ['en_attente', 'accepté'])
            ->pluck('heure_rdv')
            ->map(function ($time) {
                return substr($time, 0, 5);
            })
            ->toArray();

        $slots = [];

        for ($time = $start; $time < $end; $time += $duration) {
            $slot = date('H:i', $time);

            if (!in_array($slot, $reserved)) {
                $slots[] = $slot;
            }
        }

        return response()->json($slots);
    }
}
