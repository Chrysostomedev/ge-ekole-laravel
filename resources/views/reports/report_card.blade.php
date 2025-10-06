<!DOCTYPE html>
<html>
<head>
    <title>Bulletin de Notes - {{ $student->last_name }} {{ $student->first_name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; margin:0; padding:15px; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:2px solid #000; padding-bottom:8px;}
        .header .logo img { max-width:70px; height:auto; }
        .header .title { flex:1; text-align:center; }
        .header .title h1 { margin:0; font-size:16pt; font-weight:bold; color:#007bff; }
        .header .title h2 { margin:3px 0; font-size:12pt; }
        .header .title .infos { font-size:9pt; margin-top:4px; }
        .grades-table { width:100%; border-collapse: collapse; margin-top:10px; font-size:9pt; }
        .grades-table th, .grades-table td { border:1px solid #000; padding:5px; text-align:center; }
        .grades-table th { background-color:#007bff; color:white; }
        .summary-table { width:40%; margin-top:10px; border:1px solid #000; font-size:9pt; }
        .summary-table th, .summary-table td { padding:5px; text-align:left; }
        .footer { margin-top:20px; width:100%; text-align:center; font-size:9pt; color:#666; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">
        <img src="{{ public_path('image/clog.png') }}" alt="Logo SEPI">
    </div>
    <div class="title">
        <h1>École Primaire SEPI</h1>
        <h2>BULLETIN DE NOTES</h2>
        <div class="infos">Trimestre :1 &nbsp;&nbsp; Année scolaire : 2025 - 2026</div>
    </div>
</div>

<!-- Infos élève -->
<table style="width:100%; font-size:9pt; margin-top:5px; border-collapse: collapse;">
    <tr>
        <th style="text-align:left;">Élève :</th><td>{{ $student->last_name }} {{ $student->first_name }}</td>
        <th style="text-align:left;">Classe :</th><td>{{ $class->name }}</td>
    </tr>
    <tr>
        <th style="text-align:left;">Âge :</th><td>{{ \Carbon\Carbon::parse($student->birth_date)->age }} ans</td>
        <th style="text-align:left;">Parent :</th><td>{{ $student->parent_name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <th style="text-align:left;">Téléphone parent :</th><td>{{ $student->phone_number ?? 'N/A' }}</td>
        <th style="text-align:left;">Email parent :</th><td>{{ $student->email ?? 'N/A' }}</td>
    </tr>
    <tr>
        <th style="text-align:left;">Moyenne Générale :</th>
        <td style="color:{{ $general_average>=10?'green':'red' }}">{{ number_format($general_average,2) }}/20</td>
        <th style="text-align:left;">Rang :</th>
        <td>{{ $rank }} / {{ $class->students->count() }}</td>
    </tr>
</table>

<!-- Notes -->
<h3 style="margin-top:10px;">Détail des Résultats</h3>
<table class="grades-table">
    <thead>
        <tr>
            <th>Matière</th>
            <th>Moyenne (/20)</th>
            <th>Appréciation</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $subject => $avg)
            @php
                $moy = $avg['average'] ?? 0;
                if($moy>=16) $app='Excellent';
                elseif($moy>=14) $app='Très bien';
                elseif($moy>=12) $app='Bien';
                elseif($moy>=10) $app='Satisfaisant';
                else $app='Insuffisant';
            @endphp
            <tr>
                <td style="text-align:left;">{{ ucfirst(str_replace('_',' ',$subject)) }}</td>
                <td style="color:{{ $moy>=10?'green':'red' }}">{{ number_format($moy,2) }}</td>
                <td style="text-align:left;">{{ $app }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Synthèse -->
<table class="summary-table">
    <tr>
        <th>Moyenne de la Classe</th>
        <td>{{ number_format($class_average,2) }}/20</td>
    </tr>
    <tr>
        <th>Moyenne Max de la Classe</th>
        <td>{{ number_format($max_average,2) }}/20</td>
    </tr>
    <tr>
        <th>Moyenne Min de la Classe</th>
        <td>{{ number_format($min_average,2) }}/20</td>
    </tr>
    <tr>
        <th>Décision du Conseil</th>
        <td>
            @if($general_average>=12)
                Passage en classe supérieure avec Félicitations.
            @elseif($general_average>=10)
                Passage en classe supérieure.
            @else
                Redoublement.
            @endif
        </td>
    </tr>
</table>

<!-- Footer -->
<div class="footer">
    @isset($best_student)
        <p>Meilleur élève : {{ $best_student->last_name }} {{ $best_student->first_name }}</p>
    @endisset
    @isset($worst_student)
        <p>Moins bon élève : {{ $worst_student->last_name }} {{ $worst_student->first_name }}</p>
    @endisset
    <p>Édité le : {{ date('d/m/Y') }} | Signature Directeur : ________________________</p>
</div>

</body>
</html>
