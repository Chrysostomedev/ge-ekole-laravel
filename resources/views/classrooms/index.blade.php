@extends('layouts.app')
@section('title','Classes')
  
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des classes</h3>
  <a href="{{ route('classrooms.create') }}" class="btn btn-primary">Nouvelle classe</a>
</div>

<div class="row">
  @foreach($classrooms as $class)
    <div class="col-md-4 mb-3">
      <div class="card card-school p-3">
        <h5>{{ $class->name }}</h5>
        <p class="mb-1"><strong>Prof :</strong> {{ $class->teacher ?? '—' }}</p>
        <p class="text-muted mb-2">{{ $class->students_count }} élèves</p>
        <a href="{{ route('classrooms.show', $class) }}" class="btn btn-sm btn-outline-primary">Voir</a>
      </div>
    </div>
  @endforeach
</div>
@endsection
