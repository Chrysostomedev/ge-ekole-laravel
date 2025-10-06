@extends('layouts.app')
@section('title','Classe : ' . $classroom->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>{{ $classroom->name }}</h3>
  <div>
    <a href="{{ route('students.create') }}" class="btn btn-sm btn-primary">Inscrire un élève</a>
    <a href="{{ route('classrooms.index') }}" class="btn btn-sm btn-outline-secondary">Retour</a>
  </div>
</div>

<!-- Détails -->
<div class="card card-school p-3 mb-3">
  <strong>Niveau :</strong> {{ $classroom->level ?? '—' }}<br>
  <strong>Professeur :</strong> {{ $classroom->teacher ?? '—' }}
</div>

<!-- Liste des élèves -->
<div class="card card-school p-3">
  <h5>Élèves ({{ $classroom->students->count() }})</h5>

  @if($classroom->students->isEmpty())
    <p class="text-muted">Aucun élève dans cette classe.</p>
  @else
    <div class="table-responsive">
      <table class="table table-sm table-striped table-hover">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Notes (moy.)</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @foreach($classroom->students as $student)
          <tr>
            <td>{{ $student->full_name }}</td>
            <td>{{ $student->email ?? '—' }}</td>

            <!-- calcul rapide de la moyenne -->
            <td>
              @php
                $avg = $student->grades->avg(function($g){ return ($g->score/$g->max_score)*20; });
              @endphp
              {{ $avg ? number_format($avg,2) . ' / 20' : '—' }}
            </td>

            <td>
              <a href="{{ route('students.index', ['highlight' => $student->id]) }}#student-{{ $student->id }}" 
                 class="btn btn-sm btn-outline-primary">
                 Voir
              </a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
