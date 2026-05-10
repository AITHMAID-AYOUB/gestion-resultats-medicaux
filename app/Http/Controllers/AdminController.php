<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Patient;
use App\Models\Biologiste;
use App\Models\RendezVous;

class AdminController extends Controller
{
    public function index()
    {
        if (!session('admin_id')) {
            return redirect()->route('login.admin');
        }

        $patients = Patient::all();
        $biologistes = Biologiste::all();

        $patientsCount = $patients->count();
        $biosCount = $biologistes->count();

        $rendezvous = RendezVous::join('patients', 'rendez_vous.patient_id', '=', 'patients.id')
            ->select('rendez_vous.*', 'patients.nom', 'patients.prenom')
            ->orderByDesc('rendez_vous.id')
            ->get();

        return view('espace_admin', compact(
            'patients',
            'biologistes',
            'patientsCount',
            'biosCount',
            'rendezvous'
        ));
    }

    public function modifierStatutRdv(Request $request, $id)
    {
        if (!session('admin_id')) {
            return redirect()->route('login.admin');
        }

        RendezVous::where('id', $id)->update([
            'statut' => $request->statut,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Statut du rendez-vous modifié.');
    }

    public function deleteUser($role, $id)
    {
        if (!session('admin_id')) {
            return redirect()->route('login.admin');
        }

        if ($role === 'patient') {
            Patient::where('id', $id)->delete();
        }

        if ($role === 'biologiste') {
            Biologiste::where('id', $id)->delete();
        }

        return back()->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function addBio(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('login.admin');
        }

        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email|unique:biologistes,email',
            'password' => 'required|min:6'
        ]);

        Biologiste::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Biologiste ajouté avec succès.');
    }
}
