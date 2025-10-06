@extends('layouts.app')

@section('title', 'Saisie de Moyennes par Classe (Excel Mode)')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card shadow-lg p-4">
            <h3 class="mb-4 border-bottom pb-2 text-primary">Tableau de Saisie et Mise à Jour des Moyennes</h3>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Choix de classe --}}
            <form action="{{ route('grades.create') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end p-3 bg-light rounded border">
                    <div class="col-md-6">
                        <label for="classroom_id" class="form-label fw-bold">1. Choisir la Classe <span class="text-danger">*</span></label>
                        <select id="classroom_id" name="classroom_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">Sélectionner une classe...</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ $selectedClassId == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        @if($selectedClassId)
                            <a href="{{ route('grades.ranking') }}?classroom_id={{ $selectedClassId }}" class="btn btn-info w-100">
                                Voir le Classement Détaillé
                            </a>
                        @else
                            <button type="submit" class="btn btn-primary w-100">Afficher le tableau</button>
                        @endif
                    </div>
                </div>
            </form>

            @if($selectedClassId)
                @if($studentsData->isNotEmpty())
                    <form id="updateAveragesForm" action="{{ route('grades.storeBulk') }}" method="POST">
                        @csrf
                        <input type="hidden" name="classroom_id" value="{{ $selectedClassId }}">
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered table-hover text-center">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th>Élève</th>
                                        <th>Rang</th>
                                        @foreach($distinctSubjects as $subject)
                                            <th>{{ $subject }}<br><small>(Coeff: {{ $studentsData->first()['subject_averages'][$subject]['coeff'] ?? 1 }})</small></th>
                                        @endforeach
                                        <th class="bg-warning text-dark">Moy. Générale</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentsData as $data)
                                        <tr>
                                            <td class="text-start fw-bold">{{ $data['student']->last_name }} {{ $data['student']->first_name }}</td>
                                            <td>
                                                <span class="badge {{ $data['rank'] <= 3 ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                                    {{ $data['rank'] }}
                                                </span>
                                            </td>
                                            @foreach($distinctSubjects as $subject)
                                                @php
                                                    $currentAverage = old("averages.{$data['student']->id}.{$subject}", $data['subject_averages'][$subject]['average'] ?? '');
                                                @endphp
                                                <td>
                                                    <input type="number" step="0.01" min="0" max="20" 
                                                           name="averages[{{ $data['student']->id }}][{{ $subject }}]"
                                                           value="{{ $currentAverage }}"
                                                           class="form-control form-control-sm text-center average-input"
                                                           data-student-id="{{ $data['student']->id }}"
                                                           data-subject="{{ $subject }}"
                                                           style="width: 70px; margin:auto;">
                                                </td>
                                            @endforeach
                                            <td class="bg-warning text-dark fw-bold">
                                                <span id="avg-{{ $data['student']->id }}">{{ number_format($data['general_average'], 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('grades.index', ['classroom_id' => $selectedClassId ?? '']) }}" class="btn btn-secondary">Retour</a>

                            <button type="submit" class="btn btn-success">Sauvegarder</button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-info">Aucun élève trouvé dans cette classe.</div>
                @endif
            @else
                <div class="alert alert-warning">Veuillez sélectionner une classe pour afficher le tableau de saisie.</div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const coeffs = {!! json_encode($coefficients ?? []) !!};
    const className = "{{ $selectedClassroom->name ?? '' }}";

    function getCoeff(subject) {
        if(className && coeffs[className] && coeffs[className][subject]) return coeffs[className][subject];
        return coeffs[subject] ?? 1;
    }

    function calcAverage(studentId) {
        let total = 0, sumCoeff = 0;
        document.querySelectorAll(`.average-input[data-student-id="${studentId}"]`).forEach(input => {
            const val = parseFloat(input.value);
            const coeff = getCoeff(input.dataset.subject);
            if(!isNaN(val)) { total += val*coeff; sumCoeff += coeff; }
        });
        return sumCoeff ? (total/sumCoeff).toFixed(2) : 0;
    }

    document.querySelectorAll('.average-input').forEach(input => {
        input.addEventListener('input', function(){
            const studentId = this.dataset.studentId;
            document.getElementById(`avg-${studentId}`).textContent = calcAverage(studentId);
        });
    });
});
</script>
@endsection
