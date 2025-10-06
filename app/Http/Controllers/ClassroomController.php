<?php
namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    // Affiche toutes les classes
    public function index()
    {
        $classrooms = Classroom::withCount('students')->get();
        // withCount('students') ajoute un champ students_count utile pour l'affichage
        return view('classrooms.index', compact('classrooms'));
    }

    // Formulaire de création
    public function create()
    {
        return view('classrooms.create');
    }

    // Sauvegarde une nouvelle classe
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'nullable|string|max:100',
            'teacher' => 'nullable|string|max:255',
        ]);

        Classroom::create($data);

        return redirect()->route('classrooms.index')->with('success', 'Classe créée.');
    }

    // Affiche une classe et liste des élèves (affichage dynamique)
    public function show(Classroom $classroom)
    {
        // charge les élèves et leurs notes (évite N+1)
        $classroom->load(['students.grades']);
        return view('classrooms.show', compact('classroom'));
    }

    // Edition
    public function edit(Classroom $classroom)
    {
        return view('classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'nullable|string|max:100',
            'teacher' => 'nullable|string|max:255',
        ]);

        $classroom->update($data);
        return redirect()->route('classrooms.index')->with('success', 'Classe mise à jour.');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect()->route('classrooms.index')->with('success', 'Classe supprimée.');
    }
}
