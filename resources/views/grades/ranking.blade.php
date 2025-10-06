@extends('layouts.app')

@section('title', 'Classement et Moyennes par Classe')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card shadow-lg p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <h3 class="text-primary">Tableau de Classement des Élèves </h3>
                {{-- Bouton de retour vers l'index des moyennes --}}
                <a href="{{ route('grades.create') }}?classroom_id={{ $selectedClassId }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Retour aux Moyennes
                </a>
            </div>

            {{-- FORMULAIRE DE FILTRAGE PAR CLASSE --}}
            <form action="{{ route('grades.ranking') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end p-3 bg-light rounded border">
                    <div class="col-md-6">
                        <label for="classroom_id" class="form-label fw-bold">Choisir la Classe</label>
                        <select id="classroom_id" name="classroom_id" class="form-select">
                            <option value="">Sélectionner une classe...</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" 
                                    {{ $selectedClassId == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            Afficher le Classement Détaillé
                        </button>
                    </div>
                </div>
            </form>

            @if ($selectedClassId && $studentsWithAverages->isNotEmpty())
                <h5 class="mt-4">
                    Classement de la classe 
                    <span class="text-success fw-bold">{{ $selectedClassroom->name ?? 'N/A' }}</span> 
                </h5>
                {{-- TABLEAU DE CLASSEMENT (votre tableau dynamique) --}}
                <div class="table-responsive mt-3">
                    <table class="table table-striped table-hover align-middle mb-0 text-center">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" style="min-width: 50px;">Rang</th>
                                <th scope="col" style="min-width: 150px;">Élève</th>
                                
                                {{-- Colonnes dynamiques pour les moyennes par matière (10 colonnes ou plus si besoin) --}}
                                @foreach($distinctSubjects as $subject)
                                    <th scope="col" style="min-width: 80px;">
                                        {{ $subject }} 
                                        @php
                                            // On trouve le coefficient d'une matière donnée (doit être présent si des notes existent)
                                            $sampleData = $studentsWithAverages->first();
                                            $coeff = $sampleData['subject_averages'][$subject]['coeff'] ?? 1;
                                        @endphp
                                        <br><small class="text-info fw-normal">(Coeff: {{ $coeff }})</small>
                                    </th>
                                @endforeach

                                <th scope="col" style="min-width: 80px;">Total Coeff</th>
                                <th scope="col" style="min-width: 100px;">Moyenne Générale</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentsWithAverages as $data)
                                <tr>
                                    <td>
                                        <span class="badge {{ $data['rank'] <= 3 ? 'bg-warning text-dark' : 'bg-secondary' }} fs-6">
                                            {{ $data['rank'] }}
                                        </span>
                                    </td>
                                    <td class="text-start fw-bold">
                                        {{ $data['student']->last_name }} {{ $data['student']->first_name }}
                                    </td>
                                    
                                    {{-- Affichage des moyennes par matière --}}
                                    @foreach($distinctSubjects as $subject)
                                        @php
                                            // Récupérer la moyenne de l'élève pour cette matière
                                            $average = $data['subject_averages'][$subject]['average'] ?? null;
                                        @endphp
                                        <td>
                                            @if ($average !== null)
                                                <span class="fw-bold {{ $average < 10 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($average, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <td>
                                        <span class="badge bg-dark">{{ $data['total_coefficient'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary fs-5">
                                            {{ number_format($data['general_average'], 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif ($selectedClassId)
                <div class="alert alert-info mt-3">
                    Aucune moyenne n'a été trouvée pour la classe sélectionnée ({{ $selectedClassroom->name ?? 'N/A' }}).
                </div>
            @else
                <div class="alert alert-warning mt-3">
                    Veuillez sélectionner une classe pour afficher le tableau de classement.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection