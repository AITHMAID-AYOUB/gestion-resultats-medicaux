<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthRoleController extends Controller
{
    public function showPatientLogin()
    {
        return view('login_patient');
    }

    public function showBioLogin()
    {
        return view('login_bio');
    }

    public function showAdminLogin()
    {
        return view('login_admin');
    }

    public function loginPatient(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $patient = DB::table('patients')->where('email', $request->email)->first();

        if ($patient && Hash::check($request->password, $patient->password)) {
            session([
                'patient_id' => $patient->id,
                'patient_nom' => $patient->nom,
                'role' => 'patient'
            ]);

            return redirect()->route('espace.patient');
        }

        return back()->with('error', 'Email ou mot de passe incorrect.');
    }

    public function loginBio(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $bio = DB::table('biologistes')->where('email', $request->email)->first();

        if ($bio && Hash::check($request->password, $bio->password)) {
            session([
                'bio_id' => $bio->id,
                'bio_email' => $bio->email,
                'role' => 'biologiste'
            ]);

            return redirect()->route('espace.bio');
        }

        return back()->with('error', 'Email ou mot de passe incorrect.');
    }

    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = DB::table('administrateurs')->where('email', trim($request->email))->first();

        if ($admin && Hash::check(trim($request->password), $admin->password)) {
            session([
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'role' => 'admin'
            ]);

            return redirect()->route('espace.admin');
        }

        return back()->with('error', 'Email ou mot de passe incorrect.');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('home');
    }
    
    public function registerPatient(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email|unique:patients,email',
            'cin' => 'required|unique:patients,cin',
            'telephone' => 'required',
            'password' => 'required|confirmed|min:6'
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'cin.unique' => 'Ce CIN est déjà utilisé.',
        ]);
        DB::table('patients')->insert([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'cin' => $request->cin,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Inscription réussie !');
    }
    public function annulerRdv($id)
    {
        if (!session('patient_id')) {
            return redirect()->route('login.patient');
        }

        DB::table('rendez_vous')
            ->where('id', $id)
            ->where('patient_id', session('patient_id'))
            ->update([
                'statut' => 'annule'
            ]);

        return back()->with('success', 'Rendez-vous annulé avec succès.');
    }
}
