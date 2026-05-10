<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthRoleController;
use Carbon\Carbon;

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
        ->orderByDesc('id')
        ->get();

    $rendezvous = DB::table('rendez_vous')
        ->where('patient_id', session('patient_id'))
        ->where('date_rdv', '>=', now()->toDateString())
        ->where('statut', '!=', 'annulé')
        ->orderBy('date_rdv', 'asc')
        ->get();

    return view('espace_patient', compact('resultats', 'rendezvous'));

})->name('espace.patient');

Route::get('/patient/rdv/annuler/{id}', [AuthRoleController::class, 'annulerRdv'])
    ->name('patient.rdv.annuler');

Route::post('/patient/reserver-rdv', function (Request $request) {

    if (!session('patient_id')) {
        return redirect()->route('login.patient');
    }

    $dateChoisie = Carbon::parse($request->date_rdv);
    $today = Carbon::today();
    $maxDate = Carbon::today()->addDays(20);

    if ($dateChoisie->lt($today) || $dateChoisie->gt($maxDate)) {
        return back()->with('error', 'Vous pouvez réserver seulement dans les 20 prochains jours.');
    }

    $exists = DB::table('rendez_vous')
        ->where('date_rdv', $request->date_rdv)
        ->where('heure_rdv', $request->heure_rdv)
        ->whereIn('statut', ['en_attente', 'accepté'])
        ->exists();

    if ($exists) {
        return back()->with('error', 'Ce créneau est déjà réservé.');
    }

    DB::table('rendez_vous')->insert([
        'patient_id' => session('patient_id'),
        'date_rdv' => $request->date_rdv,
        'heure_rdv' => $request->heure_rdv,
        'motif' => $request->motif,
        'statut' => 'en_attente',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return back()->with('success', 'Rendez-vous réservé avec succès.');

})->name('patient.reserver.rdv');

/* ESPACE BIO */
Route::get('/espace/bio', function () {

    if (!session('bio_id')) {
        return redirect()->route('login.bio');
    }

    $patients = DB::table('patients')->get();

    $resultats = DB::table('resultats')
        ->join('patients', 'resultats.patient_id', '=', 'patients.id')
        ->select('resultats.*', 'patients.nom', 'patients.prenom')
        ->orderByDesc('resultats.id')
        ->get();

    $rendezvous = DB::table('rendez_vous')
        ->join('patients', 'rendez_vous.patient_id', '=', 'patients.id')
        ->where('rendez_vous.statut', 'accepté')
        ->select('rendez_vous.*', 'patients.nom', 'patients.prenom')
        ->orderByDesc('rendez_vous.id')
        ->get();

    return view('espace_bio', compact('patients', 'resultats', 'rendezvous'));

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
    ]);

    return back()->with('success', 'Résultat importé avec succès.');

})->name('bio.upload.resultat');

Route::get('/patient/available-slots', function (Request $request) {

    $date = $request->date;

    $start = strtotime('09:00');
    $end = strtotime('17:00');
    $duration = 20 * 60;

    $reserved = DB::table('rendez_vous')
        ->where('date_rdv', $date)
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
})->name('patient.available.slots');

/* ESPACE ADMIN */
Route::get('/espace/admin', function () {

    if (!session('admin_id')) {
        return redirect()->route('login.admin');
    }

    $patients = DB::table('patients')->get();
    $biologistes = DB::table('biologistes')->get();

    $patientsCount = $patients->count();
    $biosCount = $biologistes->count();

    $rendezvous = DB::table('rendez_vous')
        ->join('patients', 'rendez_vous.patient_id', '=', 'patients.id')
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

})->name('espace.admin');

/* MODIFIER STATUT RDV */
Route::post('/admin/rdv/{id}/statut', function (Request $request, $id) {

    if (!session('admin_id')) {
        return redirect()->route('login.admin');
    }

    DB::table('rendez_vous')->where('id', $id)->update([
        'statut' => $request->statut,
        'updated_at' => now(),
    ]);

    return back()->with('success', 'Statut du rendez-vous modifié.');

})->name('admin.rdv.statut');

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

    return back()->with('success', 'Utilisateur supprimé avec succès.');

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

