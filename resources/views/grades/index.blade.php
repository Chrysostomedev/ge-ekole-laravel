@extends('layouts.app')

@section('title', 'Moyennes Générales des Élèves')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Moyennes Générales des Élèves</h3>
    <a href="{{ route('grades.create') }}" class="btn btn-success shadow-sm">
        <i class="fas fa-edit me-1"></i> Saisir/Modifier les Moyennes 
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success mt-3" role="alert">
        {{ session('success') }}
    </div>
@endif

{{-- FORMULAIRE DE FILTRAGE PAR CLASSE --}}
<div class="card shadow-sm p-3 mb-4">
    <form action="{{ route('grades.index') }}" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="classroom_id" class="form-label fw-bold"> Choisir une Classe</label>
                <select id="classroom_id" name="classroom_id" class="form-select">
                    <option value="">Toutes les classes</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" 
                            {{ $selectedClassId == $classroom->id ? 'selected' : '' }}>
                            {{ $classroom->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-info shadow-sm w-100">
                    <i class="fas fa-filter me-1"></i> Afficher les Moyennes
                </button>
            </div>
            <div class="col-md-4">
                {{-- Bouton de redirection vers le classement détaillé --}}
                <a href="{{ route('grades.ranking') }}?classroom_id={{ $selectedClassId }}" class="btn btn-secondary shadow-sm w-100 {{ !$selectedClassId ? 'disabled' : '' }}">
                    Voir le Classement Détaillé <i class="fas fa-chevron-right ms-1"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card shadow-sm p-3">
    @if ($selectedClassId && $selectedClassroom)
        <h5 class="mb-3 text-primary">Moyennes Générales pour : {{ $selectedClassroom->name }}</h5>
    @endif
    
    @if($studentsWithAverages->isEmpty())
        <p class="text-center text-muted m-0 p-4">
            Aucune moyenne trouvée. {{ $selectedClassId ? 'Cette classe n\'a pas encore d\'élèves ou de moyennes enregistrées.' : 'Saisissez des moyennes ou sélectionnez une classe.' }}
        </p>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Élève</th>
                        <th scope="col">Classe</th>
                        <th scope="col" class="text-center">Coefficient Total</th>
                        <th scope="col" class="text-center">Moyenne Générale</th>
                        <th scope="col" class="text-center">Rang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentsWithAverages as $data)
                        <tr>
                            <td>
                                <a href="{{ route('students.show', $data['student']) }}" class="text-decoration-none fw-bold">
                                    {{ $data['student']->last_name }} {{ $data['student']->first_name }}
                                </a>
                            </td>
                            <td><span class="badge bg-secondary">{{ $data['student']->classroom->name ?? 'N/A' }}</span></td>
                            <td class="text-center">
                                <span class="badge bg-dark">{{ $data['total_coefficient'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-5">
                                    {{ number_format($data['average_score'], 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $data['rank'] <= 3 ? 'bg-warning text-dark' : 'bg-secondary' }} fs-6">
                                    {{ $data['rank'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection