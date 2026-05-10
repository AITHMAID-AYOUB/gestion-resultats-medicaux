<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthRoleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\BioController;
use App\Http\Controllers\AdminController;

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
Route::get('/register/patient', [AuthRoleController::class, 'showPatientRegister'])->name('register.patient');
Route::post('/register/patient', [AuthRoleController::class, 'registerPatient'])->name('register.patient.post');

/* PATIENT */
Route::get('/espace/patient', [PatientController::class, 'index'])->name('espace.patient');
Route::post('/patient/reserver-rdv', [PatientController::class, 'reserverRdv'])->name('patient.reserver.rdv');
Route::get('/patient/rdv/annuler/{id}', [PatientController::class, 'annulerRdv'])->name('patient.rdv.annuler');
Route::get('/patient/available-slots', [PatientController::class, 'availableSlots'])->name('patient.available.slots');

/* BIOLOGISTE */
Route::get('/espace/bio', [BioController::class, 'index'])->name('espace.bio');
Route::post('/bio/upload-resultat', [BioController::class, 'uploadResultat'])->name('bio.upload.resultat');

/* ADMIN */
Route::get('/espace/admin', [AdminController::class, 'index'])->name('espace.admin');
Route::post('/admin/rdv/{id}/statut', [AdminController::class, 'modifierStatutRdv'])->name('admin.rdv.statut');
Route::get('/admin/delete-user/{role}/{id}', [AdminController::class, 'deleteUser'])->name('admin.delete.user');
Route::post('/admin/add-bio', [AdminController::class, 'addBio'])->name('admin.add.bio');

/* LOGOUT */
Route::get('/logout', [AuthRoleController::class, 'logout'])->name('logout');

