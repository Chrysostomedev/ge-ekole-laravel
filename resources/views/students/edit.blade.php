@extends('layouts.app')

@section('title', 'Modifier l\'élève ' . $student->first_name . ' ' . $student->last_name)

@section('content')
<h3>Modifier l'élève : {{ $student->first_name }} {{ $student->last_name }}</h3>

{{-- Le formulaire doit pointer vers la route update, avec la méthode PUT --}}
<form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') {{-- Indique à Laravel que c'est une requête de mise à jour --}}
    
    <div class="mb-3">
        <label class="form-label">Matricule</label>
        <input name="matricule" value="{{ old('matricule', $student->matricule) }}" 
               class="form-control @error('matricule') is-invalid @enderror" required>
        @error('matricule')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input name="last_name" value="{{ old('last_name', $student->last_name) }}" 
               class="form-control @error('last_name') is-invalid @enderror" required>
        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Prénoms</label>
        <input name="first_name" value="{{ old('first_name', $student->first_name) }}" 
               class="form-control @error('first_name') is-invalid @enderror" required>
        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Email du parent (optionnel)</label>
        <input name="email" type="email" value="{{ old('email', $student->email) }}" 
               class="form-control @error('email') is-invalid @enderror">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Date de naissance</label>
        <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date) }}" 
               class="form-control @error('birth_date') is-invalid @enderror" required>
        @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Numéro de téléphone</label>
        <input type="text" name="phone_number" value="{{ old('phone_number', $student->phone_number) }}" 
               class="form-control @error('phone_number') is-invalid @enderror" required>
        @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Photo</label>
        <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror

        @if($student->photo)
            <div class="mt-2">
                <img src="{{ asset('storage/'.$student->photo) }}" alt="Photo de l'élève" class="img-thumbnail" width="120">
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label">Classe</label>
        <select name="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror">
            <option value=""> Aucune </option> 
            
            @foreach($classrooms as $c)
                @php
                    $currentClassId = old('classroom_id', $student->classroom_id);
                @endphp
                <option value="{{ $c->id }}" {{ $currentClassId == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        @error('classroom_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Annuler</a>
    </div>
</form>
@endsection
