<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Resultat;
use App\Models\RendezVous;

class BioController extends Controller
{
    public function index()
    {
        if (!session('bio_id')) {
            return redirect()->route('login.bio');
        }

        $patients = Patient::all();

        $resultats = Resultat::join('patients', 'resultats.patient_id', '=', 'patients.id')
            ->select('resultats.*', 'patients.nom', 'patients.prenom')
            ->orderByDesc('resultats.id')
            ->get();

        $rendezvous = RendezVous::join('patients', 'rendez_vous.patient_id', '=', 'patients.id')
            ->where('rendez_vous.statut', 'accepté')
            ->select('rendez_vous.*', 'patients.nom', 'patients.prenom')
            ->orderByDesc('rendez_vous.id')
            ->get();

        return view('espace_bio', compact('patients', 'resultats', 'rendezvous'));
    }

    public function uploadResultat(Request $request)
    {
        if (!session('bio_id')) {
            return redirect()->route('login.bio');
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'fichier_pdf' => 'required|mimes:pdf|max:2048',
        ]);

        $file = $request->file('fichier_pdf');
        $filename = uniqid() . '.pdf';
        $file->move(public_path('uploads'), $filename);

        Resultat::create([
            'patient_id' => $request->patient_id,
            'biologiste_id' => session('bio_id'),
            'fichier_pdf' => 'uploads/' . $filename,
            'commentaire' => $request->commentaire,
            'date_resultat' => now(),
        ]);

        return back()->with('success', 'Résultat importé avec succès.');
    }
}
