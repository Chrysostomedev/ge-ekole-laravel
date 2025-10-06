@extends('layouts.app')

@section('title','Accueil - Mon École')

@section('content')
<div class="row g-4">
    
    <div class="col-md-8">
        <div class="card card-school p-4">
            <h2>Bienvenue sur<strong>  Geek_ole</strong> , la meilleure app de suivi scolaire !</h2>
            <p class="text-muted">Gérez les classes, inscrivez les élèves, attribuez les notes, et suivez les moyennes grâce au tableau de bord.</p>

            <div class="mt-4 d-flex flex-wrap gap-2">
                {{-- 1. Lien vers le Dashboard (la vue principale des résultats) --}}
                {{-- CORRECTION : 'dashboard.index' devient 'dashboard' --}}
                <a href="{{ route('dashboard') }}" class="btn btn-primary me-2">
                    <strong>Voir le tableau d'évolution</strong>
                </a>
                
                {{-- 2. Lien vers la liste des classes (CRUD) --}}
                <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary me-2">
                  <strong>  Gérer les classes</strong>
                </a>
                
                {{-- 3. Lien vers la liste des élèves (pour Modifier/Supprimer) --}}
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary me-2">
                   <strong> Liste des élèves</strong>
                </a>
                
                {{-- 4. Lien vers l'ajout d'un nouvel élève --}}
                <a href="{{ route('students.create') }}" class="btn btn-success">
                  <strong>  Inscrire un élève</strong>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-school p-3 h-100">
            <h3><u>École</u></h3>
            <ul class="list-unstyled mb-0">
                <li class="mb-1">
                    {{-- Le chemin des modèles est correct ici car il est utilisé dans une vue Blade. --}}
                   <strong> Classes :</strong> <span class="badge bg-primary">{{ \App\Models\Classroom::count() }}</span>
                </li>
                <li class="mb-1">
                   <strong>Élèves :</strong> <span class="badge bg-info text-dark">{{ \App\Models\Student::count() }}</span>
                </li>
                <li>
                    <strong>Moyennes enregistrées :</strong><span class="badge bg-warning text-dark">{{ \App\Models\Grade::count() }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
