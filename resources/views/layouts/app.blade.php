<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Mon Ecole')</title>

  <!-- Bootstrap 5 (CDN rapide) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Dégradé bleu pour la navbar */
    .bg-gradient-blue {
      background: linear-gradient(90deg, #040533 0%, #0a157a 50%, #458de4 100%);
    }
    /* Bouton primary custom (optionnel) */
    .btn-primary-custom {
      background: linear-gradient(90deg, #03023a, #3b82f6);
      border: none;
      color: white;
    }
    /* Petit style pour les cartes */
    .card-school {
         box-shadow: 0 4px 10px rgba(5, 4, 44, 0.918);
         border: none; }
  </style>

  @stack('styles')
</head>
<body class="bg-light">

  <!-- NAVBAR : responsive en Bootstrap -->
  <nav class="navbar navbar-expand-lg bg-gradient-blue">
    <div class="container">
      <a class="navbar-brand text-white fw-bold" href="{{ route('home') }}">Geek_ole</a>

      <!-- Bouton hamburger pour mobile -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link text-white" href="{{ route('home') }}">Akwaba</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="{{ route('classrooms.index') }}">Classes</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="{{ route('students.create') }}">Élèves</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="{{ route('grades.create') }}">Moyennes</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="{{ route('dashboard') }}">Tableau d'evolution</a></li>
        </ul>
      </div> 
  </nav>

  <main class="container my-4">
    <!-- messages flash (success / error) -->
    @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    @yield('content')
  </main>

  <!-- JS Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
