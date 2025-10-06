@extends('layouts.app')
@section('title', 'Dashboard des Résultats')

@section('content')
<h3 class="mb-4">Tableau des Performances Scolaires</h3>

@foreach($classrooms as $class)
    @php
        $students = $class->rankedStudents ?? collect();
        $top = $class->topStudent ?? null;
        $bottom = $class->bottomStudent ?? null;
        $totalStudents = $class->students->count();
    @endphp

    <div class="card my-4 p-4 shadow-lg">
        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-2">
            <h4 class="mb-0 text-primary">Classe: {{ $class->name }} ({{ $totalStudents }} élèves)</h4>
        </div>

        <div class="row mb-4 g-3">
            <div class="col-md-4">
                <div class="p-3 bg-light border rounded text-center">
                    <div class="text-muted small">Moyenne de la Classe</div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($class->class_average,2) }}/20</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light border rounded text-center">
                    <div class="text-muted small">Taux de Réussite</div>
                    <div class="fs-4 fw-bold text-info">{{ number_format($class->success_rate,1) }}%</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light border rounded text-center mb-2">
                    <div class="text-muted small">Meilleur Élève ({{ number_format($top['general_average'] ?? 0,2) }}/20)</div>
                    <div class="fs-5 fw-bold text-warning">{{ $top['student']->last_name ?? 'N/A' }} {{ $top['student']->first_name ?? '' }}</div>
                </div>
                <div class="p-3 bg-light border rounded text-center">
                    <div class="text-muted small">Moins Bon Élève ({{ number_format($bottom['general_average'] ?? 0,2) }}/20)</div>
                    <div class="fs-5 fw-bold text-danger">{{ $bottom['student']->last_name ?? 'N/A' }} {{ $bottom['student']->first_name ?? '' }}</div>
                </div>
            </div>
        </div>

        <h5 class="mt-3 mb-3">Classement Détaillé</h5>
        @if($students->isEmpty() || $totalStudents===0)
            <p class="text-center text-muted">Aucun élève ou aucune moyenne enregistrée.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Élève</th>
                            <th>Moyenne (/20)</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $s)
                            @php
                                $status = $s['general_average']>=10?'Bon résultat':'Mauvais résultat';
                                $statusClass = $s['general_average']>=10?'badge bg-success':'badge bg-danger';
                                $rankClass = $s['rank']<=3?'bg-warning text-dark':'bg-light text-muted';
                            @endphp
                            <tr>
                                <td class="text-center"><span class="badge {{ $rankClass }}">{{ $s['rank'] }}</span></td>
                                <td>{{ $s['student']->last_name }} {{ $s['student']->first_name }}</td>
                                <td class="text-center fw-bold text-primary">{{ number_format($s['general_average'],2) }}</td>
                                <td class="text-center"><span class="{{ $statusClass }}">{{ $status }}</span></td>
                                <td class="text-center">
                                    <a href="{{ route('students.report_card', $s['student']->id) }}" class="btn btn-sm btn-info"><i class="fas fa-print"></i> Bulletin</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endforeach
@endsection
