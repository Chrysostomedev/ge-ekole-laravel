<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('classroom')->get();
        $highlight = $request->query('highlight');
        return view('students.index', compact('students', 'highlight'));
    }

    public function create()
    {
        $classrooms = Classroom::all();
        return view('students.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'nullable|email|max:150|unique:students,email',
            'birth_date'    => 'required|date|before:today',
            'phone_number'  => 'required|string|max:20',
            'matricule'     => 'required|string|unique:students,matricule',
            'photo'         => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'classroom_id'  => 'nullable|exists:classrooms,id',
        ]);

        // Gestion de la photo
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = Str::slug($validatedData['first_name'].'-'.$validatedData['last_name']).'-'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('students', $filename, 'public');
            $validatedData['photo'] = $path;
        }

        $student = Student::create($validatedData);

        return redirect()->route('students.index')->with('success', 'L\'élève a été inscrit avec succès (Matricule: '.$student->matricule.')');
    }

    public function edit(Student $student)
    {
        $classrooms = Classroom::all();
        return view('students.edit', compact('student', 'classrooms'));
    }

    public function update(Request $request, Student $student)
    {
        $validatedData = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'nullable|email|max:150|unique:students,email,' . $student->id,
            'birth_date'    => 'required|date|before:today',
            'phone_number'  => 'required|string|max:20',
            'matricule'     => 'required|string|unique:students,matricule,' . $student->id,
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'classroom_id'  => 'nullable|exists:classrooms,id',
        ]);

        if ($request->hasFile('photo')) {
            // Supprimer ancienne photo si existante
            if ($student->photo) Storage::disk('public')->delete($student->photo);

            $file = $request->file('photo');
            $filename = Str::slug($validatedData['first_name'].'-'.$validatedData['last_name']).'-'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('students', $filename, 'public');
            $validatedData['photo'] = $path;
        }

        $student->update($validatedData);

        return redirect()->route('students.index')->with('success', 'Les informations de l\'élève ont été mises à jour.');
    }

    public function destroy(Student $student)
    {
        if ($student->photo) Storage::disk('public')->delete($student->photo);
        $student->delete();
        return redirect()->route('students.index')->with('success', 'L\'élève a été supprimé.');
    }
}
