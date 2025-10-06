@extends('layouts.app') 
@section('title', 'Créer une classe')

@section('content')
<div class="container">
    <h2 class="mb-4">Créer une nouvelle classe</h2>

    <form action="{{ route('classrooms.store') }}" method="POST">
        @csrf
        
        {{-- Champ Nom de la classe --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nom de la classe <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Champ Niveau --}}
        <div class="mb-3">
            <label for="level" class="form-label">Niveau (ex: 3ème, Terminale)</label>
            <input type="text" name="level" id="level" class="form-control @error('level') is-invalid @enderror" value="{{ old('level') }}">
            @error('level')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Champ Professeur --}}
        <div class="mb-3">
            <label for="teacher" class="form-label">Nom du professeur principal</label>
            <input type="text" name="teacher" id="teacher" class="form-control @error('teacher') is-invalid @enderror" value="{{ old('teacher') }}">
            @error('teacher')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Créer la Classe</button>
        <a href="{{ route('classrooms.index') }}" class="btn btn-secondary mt-3">Annuler</a>
    </form>
</div>
@endsection