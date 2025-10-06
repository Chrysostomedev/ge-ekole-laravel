@extends('layouts.app')

@section('title', 'Liste des Élèves')

@section('content')
<style>
    .highlighted-student {
        animation: blink-bg 1.5s ease-in-out 3;
        background-color: #eec53f !important;
        transition: background-color 0.5s;
    }
    @keyframes blink-bg {
        0%, 100% { background-color: #f3ce54; }
        100% { background-color: #241e0d; }
    }
    tbody tr:hover {
        background-color: #cfdb29 !important; 
        cursor: pointer;
    }
    .fade-out {
        animation: fadeOut 0.5s forwards;
    }
    @keyframes fadeOut {
        to { opacity: 0; height: 0; padding: 0; margin: 0; }
    }
    .student-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Liste des Élèves</h2>
    <a href="{{ route('students.create') }}" class="btn btn-success">
         Inscrire un nouvel élève
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card p-4 shadow-sm border-0">
    @if($students->isEmpty())
        <div class="alert alert-info text-center m-0">
            Aucun élève n'est encore inscrit.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom & Prénoms</th>
                        <th>Matricule</th>
                        <th>Date de naissance</th>
                        <th>Téléphone</th>
                        <th>Classe</th>
                        <th>Email du Parent</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr id="student-{{ $student->id }}"
                            @if(isset($highlight) && $highlight == $student->id) class="highlighted-student" @endif>
                            
                            <td>
                                @if($student->photo)
                                    {{-- Affichage avec storage:link --}}
                                    <img src="{{ asset('storage/' . $student->photo) }}" 
                                         alt="Photo de {{ $student->first_name }}" 
                                         class="student-photo">
                                @else
                                    <span class="text-muted">Aucune</span>
                                @endif
                            </td>
                            <td>{{ $student->last_name }} {{ $student->first_name }}</td>
                            <td>{{ $student->matricule }}</td>
                            <td>{{ \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') }}</td>
                            <td>{{ $student->phone_number }}</td>
                            <td>
                                <span class="badge rounded-pill bg-primary">
                                    {{ $student->classroom ? $student->classroom->name : 'Non affecté' }}
                                </span>
                            </td>
                            <td>{{ $student->email ?? 'N/A' }}</td>
                            <td class="text-center d-flex justify-content-center gap-2">
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-info text-white" title="Modifier cet élève">Modifier</a>
                                <form action="{{ route('students.destroy', $student) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer cet élève">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e){
            e.preventDefault();
            if(confirm('CONFIRMATION : Voulez-vous vraiment supprimer cet élève ?')) {
                const tr = this.closest('tr');
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        _method: 'DELETE',
                        _token: this.querySelector('input[name="_token"]').value
                    })
                }).then(res => {
                    if(res.ok) {
                        tr.classList.add('fade-out');
                        setTimeout(() => tr.remove(), 500);
                    } else {
                        alert('Erreur lors de la suppression.');
                    }
                });
            }
        });
    });

    @if(isset($highlight))
        const el = document.getElementById('student-{{ $highlight }}');
        if(el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    @endif
});
</script>
@endsection
