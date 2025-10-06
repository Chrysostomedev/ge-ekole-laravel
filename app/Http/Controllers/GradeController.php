<?php
namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class GradeController extends Controller
{
    protected $coefficients = [
        'Maths' => 4,
        'Physique' => 3,
        'Chimie' => 3,
        'SVT' => 2,
        'Français' => 3,
        'Anglais' => 2,
        'Histoire' => 2,
        'Géographie' => 1,
        'Philosophie' => 2,
        'EPS' => 1,
        'Tle D1' => ['Maths' => 5, 'Physique' => 4],
    ];

public function index()
{
    // Ici, tu peux rediriger vers create() avec aucune classe sélectionnée
    return redirect()->route('grades.create');
}


    protected function getCoefficient(string $subject, string $className = null): int
    {
        if ($className && isset($this->coefficients[$className][$subject])) {
            return $this->coefficients[$className][$subject];
        }
        return $this->coefficients[$subject] ?? 1;
    }

    private function calculateAverages(Collection $students, string $className = null): Collection
    {
        $studentsData = collect();
        $subjects = array_keys(array_filter($this->coefficients, 'is_numeric'));

        foreach ($students as $student) {
            $totalWeighted = 0;
            $totalCoeff = 0;
            $subjectAverages = [];
            $gradesBySubject = $student->grades->keyBy('subject');

            foreach ($subjects as $subject) {
                $coeff = $this->getCoefficient($subject, $className);
                $grade = $gradesBySubject->get($subject);
                $score = $grade ? $grade->score : null;

                $subjectAverages[$subject] = ['average' => $score, 'coeff' => $coeff];

                if ($score !== null) {
                    $totalWeighted += $score * $coeff;
                    $totalCoeff += $coeff;
                }
            }

            $studentsData->push([
                'student' => $student,
                'subject_averages' => $subjectAverages,
                'general_average' => $totalCoeff > 0 ? round($totalWeighted / $totalCoeff, 2) : 0,
                'total_coefficient' => $totalCoeff,
            ]);
        }

        $studentsData = $studentsData->sortByDesc('general_average')->values();

        $rank = 1;
        $prevAverage = -1;
        $studentsData = $studentsData->map(function($item, $key) use (&$rank, &$prevAverage) {
            if ($item['general_average'] < $prevAverage) $rank = $key + 1;
            $item['rank'] = $rank;
            $prevAverage = $item['general_average'];
            return $item;
        });

        return $studentsData;
    }

    public function create(Request $request)
    {
        $classrooms = Classroom::orderBy('name')->get();
        $selectedClassId = $request->input('classroom_id');
        $studentsData = collect();
        $distinctSubjects = collect(array_keys(array_filter($this->coefficients, 'is_numeric')));
        $selectedClassroom = null;

        if ($selectedClassId) {
            $selectedClassroom = Classroom::find($selectedClassId);
            $students = Student::where('classroom_id', $selectedClassId)
                ->with('grades')
                ->orderBy('last_name')
                ->get();

            $studentsData = $this->calculateAverages($students, $selectedClassroom->name ?? null);
        }

        $coefficients = $this->coefficients;

        return view('grades.create', compact('classrooms', 'selectedClassId', 'studentsData', 'distinctSubjects', 'selectedClassroom', 'coefficients'));
    }

    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'averages' => 'required|array',
        ]);

        $classId = $validated['classroom_id'];
        $averages = $validated['averages'];
        $updates = 0; $inserts = 0;

        DB::beginTransaction();
        try {
            $term = 'Annuel';
            foreach ($averages as $studentId => $subjects) {
                foreach ($subjects as $subject => $score) {
                    if ($score !== null && $score !== '' && is_numeric($score) && $score >=0 && $score <=20) {
                        $coeff = $this->getCoefficient($subject, Classroom::find($classId)->name ?? null);
                        $grade = Grade::updateOrCreate(
                            ['student_id'=>$studentId,'classroom_id'=>$classId,'subject'=>$subject,'term'=>$term],
                            ['score'=>$score,'coefficient'=>$coeff,'updated_at'=>now()]
                        );
                                               $grade->wasRecentlyCreated ? $inserts++ : $updates++;
                    } else {
                        // Supprimer les notes vides si elles existent
                        Grade::where('student_id', $studentId)
                             ->where('classroom_id', $classId)
                             ->where('subject', $subject)
                             ->where('term', $term)
                             ->delete();
                    }
                }
            }

            if ($updates + $inserts > 0) {
                DB::commit();
                return redirect()->route('grades.create', ['classroom_id' => $classId])
                                 ->with('success', "Mise à jour réussie : {$updates} moyennes modifiées et {$inserts} moyennes ajoutées.");
            } else {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Aucune moyenne valide n\'a été saisie ou modifiée.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur storeBulk: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Une erreur inattendue est survenue lors de l\'enregistrement. Vérifiez les champs.');
        }
    }

    public function ranking(Request $request)
    {
        $classrooms = Classroom::orderBy('name')->get();
        $selectedClassId = $request->input('classroom_id');
        $distinctSubjects = collect(array_keys(array_filter($this->coefficients, 'is_numeric')));
        $selectedClassroom = null;
        $studentsWithAverages = collect();

        if ($selectedClassId) {
            $selectedClassroom = Classroom::find($selectedClassId);
            $students = Student::where('classroom_id', $selectedClassId)
                               ->with('grades')
                               ->get();

            $studentsWithAverages = $this->calculateAverages($students, $selectedClassroom->name ?? null);
        }

        return view('grades.ranking', compact(
            'classrooms',
            'selectedClassId',
            'studentsWithAverages',
            'distinctSubjects',
            'selectedClassroom'
        ));
    }
}
