<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
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

    protected function getCoefficient(string $subject, string $className = null): int
    {
        if ($className && isset($this->coefficients[$className][$subject])) {
            return $this->coefficients[$className][$subject];
        }
        return $this->coefficients[$subject] ?? 1;
    }

    public function index()
    {
        $classrooms = Classroom::with(['students' => function ($q) {
            $q->orderBy('last_name', 'asc')
              ->orderBy('first_name', 'asc')
              ->with('grades');
        }])->get();

        foreach ($classrooms as $class) {
            $studentsRanked = $this->calculateAverages($class->students, $class->name);

            $totalWeightedScore = 0;
            $totalCoefficientSum = 0;
            $successfulStudents = 0;

            foreach ($studentsRanked as $studentData) {
                $totalWeightedScore += $studentData['general_average'] * $studentData['total_coefficient'];
                $totalCoefficientSum += $studentData['total_coefficient'];
                if ($studentData['general_average'] >= 10) $successfulStudents++;
            }

            $class->class_average = $totalCoefficientSum > 0 
                ? round($totalWeightedScore / $totalCoefficientSum, 2) 
                : 0;

            $class->success_rate = $class->students->count() > 0
                ? round(($successfulStudents / $class->students->count()) * 100, 1)
                : 0;

            $class->rankedStudents = $studentsRanked;
            $class->topStudent = $studentsRanked->first() ?? null;
            $class->bottomStudent = $studentsRanked->last() ?? null;
        }

        return view('dashboard.index', compact('classrooms'));
    }

    public function generateReportCard(Student $student)
    {
        $student->load(['grades', 'classroom']);

        $students = Student::where('classroom_id', $student->classroom_id)
            ->with('grades')
            ->get();

        $studentsRanked = $this->calculateAverages($students, $student->classroom->name);

        $studentData = $studentsRanked->firstWhere('student.id', $student->id);
        if (!$studentData) {
            return redirect()->back()->with('error', 'Aucune donnée de classement trouvée pour cet élève.');
        }

        $results = [];
        $totalWeighted = 0;
        $totalCoeff = 0;

        foreach ($student->grades as $grade) {
            $coeff = $grade->coefficient ?? $this->getCoefficient($grade->subject, $student->classroom->name);
            $results[$grade->subject] = [
                'average' => $grade->score,
                'coeff' => $coeff,
            ];
            $totalWeighted += $grade->score * $coeff;
            $totalCoeff += $coeff;
        }

        $generalAverage = $totalCoeff > 0 ? round($totalWeighted / $totalCoeff, 2) : 0;

        $data = [
            'student' => $student,
            'class' => $student->classroom,
            'results' => $results,
            'general_average' => $generalAverage,
            'rank' => $studentData['rank'] ?? 'N/A',
            'class_average' => round($studentsRanked->avg('general_average'), 2),
            'max_average' => round($studentsRanked->max('general_average'), 2),
            'min_average' => round($studentsRanked->min('general_average'), 2),
        ];

        $pdf = Pdf::loadView('reports.report_card', $data);
        return $pdf->download("Bulletin_{$student->last_name}_{$student->first_name}_{$student->classroom->name}.pdf");
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
}
