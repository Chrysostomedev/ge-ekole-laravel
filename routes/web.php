<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\DashboardController;

// 1. Route d'Accueil
Route::get('/', function(){
    // Correction: utilise 'students.home' car la vue est dans students/home.blade.php
    return view('students.home'); 
})->name('home');

// 2. Routes de Ressources (CRUD complet)
Route::resource('classrooms', ClassroomController::class);
Route::resource('students', StudentController::class);

// --- ROUTES PERSONNALISÉES POUR GRADE (Pour la saisie en masse) ---

// Doivent être placées avant la Route::resource pour prendre la priorité.

// 2.1. Nouvelle Route pour l'Affichage du Classement et des Moyennes
// L'URL de base pour la moyenne et le classement de l'école.
Route::get('/grades/ranking', [GradeController::class, 'ranking'])->name('grades.ranking');

// 2.2. Routes Saisie en Masse (Formulaire et Soumission)
Route::get('/grades/create', [GradeController::class, 'create'])->name('grades.create');
Route::post('/grades/store-bulk', [GradeController::class, 'storeBulk'])->name('grades.storeBulk');

// 2.3. Nouvelle Route AJAX pour récupérer les élèves par classe
Route::get('/grades/students-by-class/{classroom}', [GradeController::class, 'getStudentsByClass'])->name('grades.studentsByClass');


// --- ROUTE DE RESSOURCE GRADE (Pour le reste du CRUD: index, show, edit, update, destroy) ---

// On garde ici les autres routes de ressource pour le CRUD individuel des notes.
// On exclut `create` et `store` car on utilise les versions "bulk" au-dessus.
Route::resource('grades', GradeController::class)->except(['create', 'store']);

// ... vos autres routes

// Route pour le Dashboard
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

// Nouvelle Route pour générer le bulletin (à lier à la page élève)
// On utilise 'students' comme préfixe si la route est liée à l'étudiant
Route::get('/students/{student}/report-card', [App\Http\Controllers\DashboardController::class, 'generateReportCard'])
    ->name('students.report_card');

// ...