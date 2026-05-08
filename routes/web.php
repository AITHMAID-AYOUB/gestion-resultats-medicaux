<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthRoleController;

/* HOME */
Route::get('/', function () {
    return view('welcome');
})->name('home');


/* LOGIN */
Route::get('/login/patient', [AuthRoleController::class, 'showPatientLogin'])->name('login.patient');
Route::post('/login/patient', [AuthRoleController::class, 'loginPatient'])->name('login.patient.post');

Route::get('/login/bio', [AuthRoleController::class, 'showBioLogin'])->name('login.bio');
Route::post('/login/bio', [AuthRoleController::class, 'loginBio'])->name('login.bio.post');

Route::get('/login/admin', [AuthRoleController::class, 'showAdminLogin'])->name('login.admin');
Route::post('/login/admin', [AuthRoleController::class, 'loginAdmin'])->name('login.admin.post');


/* REGISTER */
Route::get('/register/patient', function () {
    return view('register_patient');
})->name('register.patient');

Route::post('/register/patient', [AuthRoleController::class, 'registerPatient'])
->name('register.patient.post');


/* ESPACE PATIENT */
Route::get('/espace/patient', function () {

    if (!session('patient_id')) {
        return redirect()->route('login.patient');
    }

    $resultats = DB::table('resultats')
        ->where('patient_id', session('patient_id'))
        ->get();

    return view('espace_patient', compact('resultats'));

})->name('espace.patient');


/* ESPACE BIO */
Route::get('/espace/bio', function () {

    if (!session('bio_id')) {
        return redirect()->route('login.bio');
    }

    $patients = DB::table('patients')->get();

    $resultats = DB::table('resultats')
        ->join('patients', 'resultats.patient_id', '=', 'patients.id')
        ->select(
            'resultats.*',
            'patients.nom',
            'patients.prenom'
        )
        ->orderBy('resultats.id', 'desc')
        ->get();

    $rendezvous = DB::table('rendez_vous')
        ->join('patients', 'rendez_vous.patient_id', '=', 'patients.id')
        ->select(
            'rendez_vous.*',
            'patients.nom',
            'patients.prenom'
        )
        ->orderBy('rendez_vous.id', 'desc')
        ->get();

    return view('espace_bio', compact(
        'patients',
        'resultats',
        'rendezvous'
    ));

})->name('espace.bio');


/* UPLOAD PDF */
Route::post('/bio/upload-resultat', function (Request $request) {

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

    DB::table('resultats')->insert([
        'patient_id' => $request->patient_id,
        'biologiste_id' => session('bio_id'),
        'fichier_pdf' => 'uploads/' . $filename,
        'commentaire' => $request->commentaire,
        'date_resultat' => now(),
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return back()->with('success', 'Upload OK');

})->name('bio.upload.resultat');


/* ESPACE ADMIN */
Route::get('/espace/admin', function () {
    if (!session('admin_id')) {
        return redirect()->route('login.admin');
    }

    $patientsCount = DB::table('patients')->count();
    $biosCount = DB::table('biologistes')->count();

    $patients = DB::table('patients')
        ->select('id', 'nom', 'prenom', 'email', DB::raw("'patient' as role"));

    $bios = DB::table('biologistes')
        ->select('id', 'nom', 'prenom', 'email', DB::raw("'biologiste' as role"));

    $users = $patients->union($bios)->get();

    return view('espace_admin', compact('users', 'patientsCount', 'biosCount'));
})->name('espace.admin');


/* DELETE USER */
Route::get('/admin/delete-user/{role}/{id}', function ($role, $id) {

    if (!session('admin_id')) {
        return redirect()->route('login.admin');
    }

    if ($role === 'patient') {
        DB::table('patients')->where('id', $id)->delete();
    }

    if ($role === 'biologiste') {
        DB::table('biologistes')->where('id', $id)->delete();
    }

    return back()->with('success', 'Supprimé');

})->name('admin.delete.user');

/* AJOUT BIO */
Route::post('/admin/add-bio', function (Request $request) {
    if (!session('admin_id')) {
        return redirect()->route('login.admin');
    }

    $request->validate([
        'nom' => 'required',
        'prenom' => 'required',
        'email' => 'required|email|unique:biologistes,email',
        'password' => 'required|min:6'
    ]);

    DB::table('biologistes')->insert([
        'nom' => $request->nom,
        'prenom' => $request->prenom,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return back()->with('success', 'Biologiste ajouté avec succès.');
})->name('admin.add.bio');

/* LOGOUT */
Route::get('/logout', [AuthRoleController::class, 'logout'])->name('logout');


