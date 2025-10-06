@extends('layouts.app')
@section('title','Inscrire un élève')

@section('content')
<h3>Inscrire un élève</h3>

<form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="mb-3">
    <label class="form-label">Nom</label>
    <input name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" required>
    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Prénoms</label>
    <input name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" required>
    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Email du parent (optionnel)</label>
    <input name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Matricule de l'élève</label>
    <input name="matricule" value="{{ old('matricule') }}" class="form-control @error('matricule') is-invalid @enderror" required>
    @error('matricule')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Date de naissance</label>
    <input name="birth_date" type="date" value="{{ old('birth_date') }}" class="form-control @error('birth_date') is-invalid @enderror" required>
    @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Numéro de téléphone</label>
    <input name="phone_number" type="tel" value="{{ old('phone_number') }}" class="form-control @error('phone_number') is-invalid @enderror" required>
    @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Photo de l'élève</label>
    <input name="photo" type="file" class="form-control @error('photo') is-invalid @enderror" required>
    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Classe</label>
    <select name="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror">
      <option value="">Aucune</option> 
      @foreach($classrooms as $c)
        <option value="{{ $c->id }}" {{ old('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
      @endforeach
    </select>
    @error('classroom_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <button class="btn btn-primary">Inscrire</button>
</form>
@endsection
